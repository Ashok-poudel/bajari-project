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
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
<div class="logo-area">
<img src="assets/images/photos/logo.png" class="logo">
<h1>BAJARI STORE</h1>
</div>
<nav>
  <input type="text" id="search" placeholder="Search products..." onkeyup="searchProduct()">
  <a href="index.php">Home</a>
  <a href="aboutus.html">About Us</a>
  <?php if ($userName): ?>
    <div class="user-profile">
      <span class="user-avatar"><?php echo strtoupper(substr(htmlspecialchars($userName), 0, 1)); ?></span>
      <span class="user-greeting">Hello, <?php echo htmlspecialchars($userName); ?></span>
    </div>
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
  <?php if ($userName): ?>
    <div class="hero-welcome">Welcome back, <?php echo htmlspecialchars($userName); ?>!</div>
  <?php endif; ?>
  <div class="typing-wrap">
    <span id="type-text"></span><span class="cursor">|</span>
  </div>
  <p class="hero-subtitle">Shop your favourite items now</p>
  <a href="#product-container" class="cta-btn">Browse Latest Deals</a>
</div>
</section>
<section class="features">
  <div class="section-header">
    <h2>Why Bajari Store?</h2>
    <p>Trusted shopping with fast delivery, real stock, and secure checkout.</p>
  </div>
  <div class="feature-grid">
    <div class="feature-card">
      <h3>Trusted Quality</h3>
      <p>Handpicked products with real stock updates and transparent pricing.</p>
    </div>
    <div class="feature-card">
      <h3>Fast Delivery</h3>
      <p>Reliable shipping across Nepal so your order reaches you quickly.</p>
    </div>
    <div class="feature-card">
      <h3>Secure Payments</h3>
      <p>Checkout safely with Khalti, eSewa, or card integration.</p>
    </div>
  </div>
</section>
<section class="products">
  <div class="products-header">
    <h2>Latest Deals</h2>
    <p>Explore in-stock items, limited offers, and top-selling products.</p>
  </div>
  <div id="product-container" class="product-grid"></div>
</section>
<div id="alert-box"></div>
<script src="assets/js/script.js"></script>
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
