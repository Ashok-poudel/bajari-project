<?php
header('Content-Type: application/json');
include 'db.php';

$products = [];
$result = $conn->query("SELECT id, name, price, image, IFNULL(stock, 0) AS stock FROM products ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['stock'] = intval($row['stock']);
        $row['is_sold_out'] = $row['stock'] <= 0;
        $products[] = $row;
    }
    echo json_encode(['success' => true, 'products' => $products]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database query failed: ' . $conn->error,
        'products' => []
    ]);
}
$conn->close();
?>