<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$session_id = session_id();
$ownerClause = $user_id ? "user_id = $user_id" : "session_id = '" . $conn->real_escape_string($session_id) . "'";

$sql = "SELECT c.product_id, c.quantity, p.price FROM carts c JOIN products p ON p.id = c.product_id WHERE $ownerClause";
$result = $conn->query($sql);
$items = [];
$total = 0.0;
while ($row = $result->fetch_assoc()) {
    $price = floatval($row['price']);
    $quantity = intval($row['quantity']);
    $items[] = ['product_id' => intval($row['product_id']), 'quantity' => $quantity, 'price' => $price];
    $total += $price * $quantity;
}

if (count($items) === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

if ($user_id) {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, session_id, total_amount, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->bind_param('isd', $user_id, $session_id, $total);
} else {
    $stmt = $conn->prepare("INSERT INTO orders (session_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())");
    $stmt->bind_param('sd', $session_id, $total);
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Could not create order']);
    exit;
}

$order_id = $conn->insert_id;
$stmt->close();

$itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
    $itemStmt->bind_param('iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
    $itemStmt->execute();
}
$itemStmt->close();

$conn->query("DELETE FROM carts WHERE $ownerClause");

echo json_encode(['success' => true, 'order_id' => $order_id]);
$conn->close();
?>