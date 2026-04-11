<?php
header('Content-Type: application/json');
include 'db.php';

$result = $conn->query("SELECT id, name, price, image FROM products ORDER BY id ASC");
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode(['success' => true, 'products' => $products]);
$conn->close();
?>