<?php
header('Content-Type: application/json');
include 'db.php';

$result = $conn->query("SELECT id, name, price, image, stock FROM products ORDER BY id ASC");
$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['stock'] = intval($row['stock']);
        $row['is_sold_out'] = $row['stock'] <= 0;
        $products[] = $row;
    }
}

echo json_encode(['success' => true, 'products' => $products]);
$conn->close();
?>