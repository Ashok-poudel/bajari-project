<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . ($connection_error ?? 'Unknown error')]);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$session_id = session_id();
$ownerClause = $user_id ? "user_id = $user_id" : "session_id = '" . $conn->real_escape_string($session_id) . "'";

$sql = "SELECT c.product_id, c.quantity, p.price, p.stock, p.name FROM carts c JOIN products p ON p.id = c.product_id WHERE $ownerClause";
$result = $conn->query($sql);
$items = [];
$total = 0.0;
while ($row = $result->fetch_assoc()) {
    $price = floatval($row['price']);
    $quantity = intval($row['quantity']);
    $stock = intval($row['stock']);
    if ($quantity > $stock) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock for ' . $row['name']]);
        exit;
    }
    if ($stock <= 0) {
        echo json_encode(['success' => false, 'message' => 'Product ' . $row['name'] . ' is sold out']);
        exit;
    }
    $items[] = ['product_id' => intval($row['product_id']), 'quantity' => $quantity, 'price' => $price];
    $total += $price * $quantity;
}

if (count($items) === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$conn->begin_transaction();
try {
    if ($user_id) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, session_id, total_amount, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->bind_param('isd', $user_id, $session_id, $total);
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (session_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->bind_param('sd', $session_id, $total);
    }

    if (!$stmt->execute()) {
        throw new Exception('Could not create order');
    }

    $order_id = $conn->insert_id;
    $stmt->close();

    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $itemStmt->bind_param('iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
        if (!$itemStmt->execute()) {
            throw new Exception('Could not save order items');
        }
        $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $updateStock->bind_param('iii', $item['quantity'], $item['product_id'], $item['quantity']);
        $updateStock->execute();
        if ($updateStock->affected_rows === 0) {
            throw new Exception('Stock update failed for product ID ' . $item['product_id']);
        }
        $updateStock->close();
    }
    $itemStmt->close();

    $conn->query("DELETE FROM carts WHERE $ownerClause");
    $conn->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>