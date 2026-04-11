<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$session_id = session_id();

function getOwnerClause($conn, $user_id, $session_id) {
    if ($user_id) {
        return "user_id = " . intval($user_id);
    }
    return "session_id = '" . $conn->real_escape_string($session_id) . "'";
}

$ownerClause = getOwnerClause($conn, $user_id, $session_id);
$action = $_REQUEST['action'] ?? '';

function sendResponse($data) {
    echo json_encode($data);
    exit;
}

if ($action === 'get') {
    $sql = "SELECT c.product_id, p.name, p.price, p.image, c.quantity FROM carts c JOIN products p ON p.id = c.product_id WHERE $ownerClause";
    $result = $conn->query($sql);
    $items = [];
    $total = 0;
    $totalQty = 0;
    while ($row = $result->fetch_assoc()) {
        $row['price'] = floatval($row['price']);
        $row['quantity'] = intval($row['quantity']);
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $items[] = $row;
        $total += $row['subtotal'];
        $totalQty += $row['quantity'];
    }

    sendResponse(['success' => true, 'items' => $items, 'total' => $total, 'totalQty' => $totalQty]);
}

if ($action === 'add') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id <= 0 || $quantity <= 0) {
        sendResponse(['success' => false, 'message' => 'Invalid product or quantity']);
    }

    $sql = "SELECT quantity FROM carts WHERE product_id = $product_id AND $ownerClause";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newQuantity = intval($row['quantity']) + $quantity;
        $conn->query("UPDATE carts SET quantity = $newQuantity WHERE product_id = $product_id AND $ownerClause");
    } else {
        $product_stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
        $product_stmt->bind_param('i', $product_id);
        $product_stmt->execute();
        $product_stmt->store_result();
        if ($product_stmt->num_rows === 0) {
            sendResponse(['success' => false, 'message' => 'Product not found']);
        }
        $product_stmt->close();

        $session = $conn->real_escape_string($session_id);
        $sql = "INSERT INTO carts (session_id, user_id, product_id, quantity, updated_at) VALUES ('$session', " . ($user_id ? intval($user_id) : 'NULL') . ", $product_id, $quantity, NOW())";
        $conn->query($sql);
    }

    sendResponse(['success' => true, 'message' => 'Product added to cart']);
}

if ($action === 'update') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : null;
    $change = isset($_POST['change']) ? intval($_POST['change']) : 0;

    if ($product_id <= 0) {
        sendResponse(['success' => false, 'message' => 'Invalid product']);
    }

    $result = $conn->query("SELECT quantity FROM carts WHERE product_id = $product_id AND $ownerClause");
    if (!$result || $result->num_rows === 0) {
        sendResponse(['success' => false, 'message' => 'Cart item not found']);
    }

    $row = $result->fetch_assoc();
    $currentQuantity = intval($row['quantity']);
    if ($quantity !== null) {
        $newQuantity = $quantity;
    } else {
        $newQuantity = $currentQuantity + $change;
    }

    if ($newQuantity <= 0) {
        $conn->query("DELETE FROM carts WHERE product_id = $product_id AND $ownerClause");
        sendResponse(['success' => true, 'message' => 'Item removed']);
    }

    $conn->query("UPDATE carts SET quantity = $newQuantity, updated_at = NOW() WHERE product_id = $product_id AND $ownerClause");
    sendResponse(['success' => true, 'message' => 'Cart updated']);
}

if ($action === 'remove') {
    $product_id = intval($_POST['product_id'] ?? 0);
    if ($product_id <= 0) {
        sendResponse(['success' => false, 'message' => 'Invalid product']);
    }
    $conn->query("DELETE FROM carts WHERE product_id = $product_id AND $ownerClause");
    sendResponse(['success' => true, 'message' => 'Item removed from cart']);
}

if ($action === 'clear') {
    $conn->query("DELETE FROM carts WHERE $ownerClause");
    sendResponse(['success' => true, 'message' => 'Cart cleared']);
}

sendResponse(['success' => false, 'message' => 'Unknown action']);
?>