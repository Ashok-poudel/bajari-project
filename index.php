<?php
session_start();
$userName = $_SESSION['user_name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bajari Store</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
<div class="logo-area">
<img src="photos/logo.png" class="logo">
<h1>BAJARI STORE</h1>
</div>
<nav>
  <input type="text" id="search" placeholder="Search products..." onkeyup="searchProduct()">
  <a href="index.php">Home</a>
  <a href="aboutus.html">About Us</a>
  <?php if ($userName): ?>
    <span class="user-greeting">Hello, <?php echo htmlspecialchars($userName); ?></span>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a>
    <a href="signup.php">Signup</a>
  <?php endif; ?>
  <a href="cart.html" class="cart-btn">
    Cart (<span id="cart-count">0</span>)
  </a>
</nav>
</header>

<section class="hero">
<div class="overlay"></div>
<div class="hero-text">
<h1>Best Products At Best Price</h1>
<p>Shop your favourite items now</p>
</div>
</section>
<section class="products" id="product-container"></section>
<div id="alert-box"></div>
<script src="script.js"></script>
<footer class="footer">
  <div class="footer-container">

    <div class="footer-section">
      <h2 class="logo">Bajari Store</h2>
      <p>Your trusted online shopping platform in Nepal 🇳🇵</p>
    </div>

    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="cart.html">Cart</a></li>
        <li><a href="#">Products</a></li>
        <li><a href="aboutus.html">About Us</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h3>Contact</h3>
      <p>Email: support@bajari.com</p>
      <p>Phone: +977 9817196469</p>
    </div>

  </div>

  <div class="footer-bottom">
    <p>© 2026 Bajari Store | All Rights Reserved</p>
  </div>
</footer>
</body>
</html>
