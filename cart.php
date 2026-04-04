<?php
session_start();
include 'db.php';

// Initialize cart if not exists
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Handle add/remove/change quantity
if(isset($_GET['action']) && isset($_GET['index'])){
    $index = intval($_GET['index']);
    switch($_GET['action']){
        case 'add':
            if(isset($_SESSION['cart'][$index])){
                $_SESSION['cart'][$index]['quantity'] += 1;
            } else {
                // Add new product from JS (sent via index)
                $_SESSION['cart'][$index] = $_SESSION['products'][$index] ?? null;
                if($_SESSION['cart'][$index]) $_SESSION['cart'][$index]['quantity'] = 1;
            }
            break;
        case 'minus':
            $_SESSION['cart'][$index]['quantity'] -= 1;
            if($_SESSION['cart'][$index]['quantity'] <= 0){
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            break;
        case 'remove':
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
    }
    header("Content-Type: text/plain");
    echo "success";
    exit();
}

// Calculate total
function getTotal($cart){
    $total = 0;
    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}
?>