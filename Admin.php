<?php
session_start();
include 'db.php';

$adminEmail = 'poudelashok77@gmail.com';
$adminPassword = 'Ashok@123';
$error = '';
$message = '';
$showDashboard = false;

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: Admin.php');
    exit;
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $showDashboard = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($email !== $adminEmail || $password !== $adminPassword) {
        $error = 'Invalid admin email or password.';
    } else {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        $showDashboard = true;
        header('Location: Admin.php');
        exit;
    }
}

if ($showDashboard) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] !== 'admin_login') {
        if ($_POST['action'] === 'add_product') {
            $name = trim($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $image = trim($_POST['image'] ?? '');

            if ($name === '' || $price <= 0 || $image === '') {
                $error = 'Please fill in all fields correctly.';
            } else {
                $stmt = $conn->prepare('INSERT INTO products (name, price, image) VALUES (?, ?, ?)');
                $stmt->bind_param('sds', $name, $price, $image);
                if ($stmt->execute()) {
                    $message = 'Product added successfully.';
                } else {
                    $error = 'Could not add product.';
                }
                $stmt->close();
                header('Location: Admin.php?section=add');
                exit;
            }
        }

        if ($_POST['action'] === 'delete_product') {
            $productId = intval($_POST['product_id'] ?? 0);
            if ($productId > 0) {
                $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
                $stmt->bind_param('i', $productId);
                if ($stmt->execute()) {
                    $message = 'Product deleted successfully.';
                } else {
                    $error = 'Could not delete product.';
                }
                $stmt->close();
            } else {
                $error = 'Invalid product selection.';
            }
            header('Location: Admin.php?section=products');
            exit;
        }

        if ($_POST['action'] === 'update_order_status') {
            $orderId = intval($_POST['order_id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
            if ($orderId > 0 && in_array($status, $allowedStatuses, true)) {
                $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
                $stmt->bind_param('si', $status, $orderId);
                if ($stmt->execute()) {
                    $message = 'Order status updated.';
                } else {
                    $error = 'Could not update order status.';
                }
                $stmt->close();
            } else {
                $error = 'Invalid order or status.';
            }
            header('Location: Admin.php?section=orders');
            exit;
        }
    }

    $products = [];
    $result = $conn->query('SELECT id, name, price, image FROM products ORDER BY id DESC');
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    $orderCount = 0;
    $orderRows = [];
    $orderResult = $conn->query('SELECT COUNT(*) AS total_orders FROM orders');
    if ($orderResult) {
        $orderCount = intval($orderResult->fetch_assoc()['total_orders']);
    }

    $ordersResult = $conn->query('SELECT o.id, o.total_amount, o.status, o.created_at, u.email FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 20');
    if ($ordersResult) {
        while ($row = $ordersResult->fetch_assoc()) {
            $orderRows[] = $row;
        }
    }

    $userRows = [];
    $usersResult = $conn->query('SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 50');
    if ($usersResult) {
        while ($row = $usersResult->fetch_assoc()) {
            $userRows[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
<style>
body { background: #f4f5f7; }
.admin-container { display: flex; gap: 20px; padding: 30px; }
.sidebar { width: 220px; background: #111; color: white; border-radius: 15px; padding: 20px; height: fit-content; }
.sidebar h2 { margin-bottom: 20px; }
.sidebar a { display: block; margin: 10px 0; padding: 10px; border-radius: 8px; color: white; text-decoration: none; cursor: pointer; }
.sidebar a:hover { background: #333; }
.content { flex: 1; }
.admin-card { background: white; padding: 20px; border-radius: 18px; box-shadow: 0 6px 18px rgba(0,0,0,0.1); margin-bottom: 20px; }
.admin-product { display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 15px; border-radius: 10px; margin-bottom: 10px; }
.admin-product img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
.delete-btn { background: red; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; }
.delete-btn:hover { background: darkred; }
.admin-card input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 10px; border: 1px solid #ccc; }
.admin-card button, .login-card button { width: 100%; padding: 12px; border: none; border-radius: 10px; background: #111; color: white; cursor: pointer; }
.admin-card button:hover, .login-card button:hover { background: #333; }
.order-table { width: 100%; border-collapse: collapse; }
.order-table th, .order-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
.order-table th { background: #f1f1f1; }
.login-wrapper { max-width: 420px; margin: 80px auto; }
.login-card { background: white; padding: 30px; border-radius: 18px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
.login-card h2 { margin-bottom: 20px; }
.login-card label { display: block; margin: 12px 0 6px; font-weight: 600; }
.login-card input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 10px; }
.error-message { color: #b00020; margin-bottom: 15px; }
.success-message { color: #1d6f2f; margin-bottom: 15px; }
.logout-link { display: inline-block; margin-top: 10px; color: #111; text-decoration: none; font-weight: bold; }
.user-status { margin-bottom: 15px; font-weight: 600; }
</style>
</head>
<body>
<?php if (!$showDashboard): ?>
<div class="login-wrapper">
  <div class="login-card">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" onsubmit="return validateAdminLogin();">
      <input type="hidden" name="action" value="admin_login">
      <label for="admin-email">Email</label>
      <input id="admin-email" type="email" name="email" placeholder="Enter admin email" required>
      <label for="admin-password">Password</label>
      <input id="admin-password" type="password" name="password" placeholder="Enter password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</div>
<script>
function validateAdminLogin() {
  const email = document.getElementById('admin-email').value.trim();
  const password = document.getElementById('admin-password').value;
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!email) {
    alert('Please enter your admin email.');
    return false;
  }
  if (!emailPattern.test(email)) {
    alert('Enter a valid email address.');
    return false;
  }
  if (!password) {
    alert('Please enter your password.');
    return false;
  }
  if (password.length < 8) {
    alert('Password must be at least 8 characters long.');
    return false;
  }
  return true;
}
</script>
<?php else: ?>
<header style="padding: 20px; background:#111; color:white; display:flex; justify-content:space-between; align-items:center;">
  <div style="display:flex; align-items:center; gap:15px;">
    <img src="photos/logo.png" alt="Logo" style="height:44px;">
    <div>
      <h1 style="margin:0; font-size:24px;">Admin Dashboard</h1>
      <div class="user-status">Logged in as <?php echo htmlspecialchars($_SESSION['admin_email']); ?></div>
    </div>
  </div>
  <div>
    <a class="logout-link" href="Admin.php?logout=1">Logout</a>
  </div>
</header>
<div class="admin-container">
  <div class="sidebar">
    <h2>Dashboard</h2>
    <a onclick="showSection('products')">📦 Products</a>
    <a onclick="showSection('add')">➕ Add Product</a>
    <a onclick="showSection('orders')">🛒 Orders</a>
    <a onclick="showSection('users')">👥 Users</a>
  </div>

  <div class="content">
    <?php if ($message): ?>
      <div class="admin-card" style="background:#e6ffed; color:#134f2d;">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="admin-card" style="background:#ffe6e6; color:#861919;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <div id="products" class="admin-card">
      <h2>All Products</h2>
      <?php if (count($products) === 0): ?>
        <p>No products found.</p>
      <?php endif; ?>
      <div id="product-list">
        <?php foreach ($products as $product): ?>
          <div class="admin-product">
            <div style="display:flex; gap:15px; align-items:center;">
              <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
              <div>
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <p>Rs <?php echo htmlspecialchars($product['price']); ?></p>
              </div>
            </div>
            <form method="POST" style="margin:0;">
              <input type="hidden" name="product_id" value="<?php echo intval($product['id']); ?>">
              <input type="hidden" name="action" value="delete_product">
              <button type="submit" class="delete-btn">Delete</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div id="add" class="admin-card" style="display:none;">
      <h2>Add Product</h2>
      <form method="POST" onsubmit="return validateProductForm();">
        <input type="hidden" name="action" value="add_product">
        <input type="text" name="name" id="product-name" placeholder="Product Name" required>
        <input type="number" step="0.01" name="price" id="product-price" placeholder="Price" required>
        <input type="text" name="image" id="product-image" placeholder="Image path (photos/item.jpg)" required>
        <button type="submit">Add Product</button>
      </form>
    </div>

    <div id="orders" class="admin-card" style="display:none;">
      <h2>Orders</h2>
      <p>Total Orders: <strong id="order-count"><?php echo $orderCount; ?></strong></p>
      <table class="order-table">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>User Email</th>
            <th>Total</th>
            <th>Status</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($orderRows) === 0): ?>
            <tr><td colspan="5">No orders found.</td></tr>
          <?php else: ?>
            <?php foreach ($orderRows as $order): ?>
              <tr>
                <td><?php echo intval($order['id']); ?></td>
                <td><?php echo htmlspecialchars($order['email'] ?: 'Guest'); ?></td>
                <td>Rs <?php echo htmlspecialchars($order['total_amount']); ?></td>
                <td>
                  <form method="POST" style="display:flex; gap:8px; align-items:center;">
                    <input type="hidden" name="action" value="update_order_status">
                    <input type="hidden" name="order_id" value="<?php echo intval($order['id']); ?>">
                    <select name="status" style="padding:8px; border-radius:8px; border:1px solid #ccc;">
                      <?php foreach (['pending', 'processing', 'completed', 'cancelled'] as $statusOption): ?>
                        <option value="<?php echo $statusOption; ?>" <?php echo $order['status'] === $statusOption ? 'selected' : ''; ?>><?php echo ucfirst($statusOption); ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit" style="padding: 8px 12px; border-radius: 8px; background:#111; color:white; border:none; cursor:pointer;">Save</button>
                  </form>
                </td>
                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div id="users" class="admin-card" style="display:none;">
      <h2>Users</h2>
      <p>Total Users: <strong><?php echo count($userRows); ?></strong></p>
      <table class="order-table">
        <thead>
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined At</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($userRows) === 0): ?>
            <tr><td colspan="4">No users found.</td></tr>
          <?php else: ?>
            <?php foreach ($userRows as $user): ?>
              <tr>
                <td><?php echo intval($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
function showSection(id) {
  document.getElementById('products').style.display = 'none';
  document.getElementById('add').style.display = 'none';
  document.getElementById('orders').style.display = 'none';
  document.getElementById(id).style.display = 'block';
}

function validateProductForm() {
  const name = document.getElementById('product-name').value.trim();
  const price = parseFloat(document.getElementById('product-price').value);
  const image = document.getElementById('product-image').value.trim();

  if (!name) {
    alert('Please enter a product name.');
    return false;
  }
  if (isNaN(price) || price <= 0) {
    alert('Please enter a valid price greater than 0.');
    return false;
  }
  if (!image) {
    alert('Please enter an image path.');
    return false;
  }
  return true;
}

showSection('<?php echo htmlspecialchars($_GET['section'] ?? 'products'); ?>');
</script>
<?php endif; ?>
</body>
</html>
