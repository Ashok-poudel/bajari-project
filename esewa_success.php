<?php
// Start session if you store cart in session
session_start();

// Optionally, verify eSewa payment with their API (recommended for live)
// For now, we just redirect after success

// Clear cart if stored in session
if(isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect to thank you page
header("Location: thankyou.html");
exit();
?>