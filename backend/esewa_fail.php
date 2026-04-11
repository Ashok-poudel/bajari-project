<?php
// Start session if needed
session_start();

// Optionally, log failed payment details for record
// You can access order ID via $_POST['pid'] if eSewa posts it

// Redirect to checkout page or show message
header("Location: checkout.html?status=fail");
exit();
?>