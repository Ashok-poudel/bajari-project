<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">

<style>

/* Admin Layout */
.admin-container {
  display: flex;
  gap: 20px;
  padding: 30px;
}

/* Sidebar */
.sidebar {
  width: 220px;
  background: #111;
  color: white;
  border-radius: 15px;
  padding: 20px;
  height: fit-content;
}

.sidebar h2 {
  margin-bottom: 20px;
}

.sidebar a {
  display: block;
  margin: 10px 0;
  padding: 10px;
  border-radius: 8px;
  color: white;
  text-decoration: none;
  cursor: pointer;
}

.sidebar a:hover {
  background: #333;
}

/* Content */
.content {
  flex: 1;
}

/* Card */
.admin-card {
  background: white;
  padding: 20px;
  border-radius: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

/* Product List */
.admin-product {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9f9f9;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 10px;
}

.admin-product img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
}

/* Buttons */
.delete-btn {
  background: red;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 8px;
  cursor: pointer;
}

.delete-btn:hover {
  background: darkred;
}

/* Inputs */
.admin-card input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border-radius: 10px;
  border: 1px solid #ccc;
}

.admin-card button {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 10px;
  background: #111;
  color: white;
  cursor: pointer;
}

.admin-card button:hover {
  background: #333;
}

</style>
</head>

<body>

<!-- HEADER (same as your site) -->
<header>
  <div class="logo-area">
    <img src="photos/logo.png" class="logo">
    <h1>Admin Panel</h1>
  </div>

  <nav>
    <a href="index.html">Home</a>
    <a href="admin.html">Admin</a>
  </nav>
</header>

<div class="admin-container">

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Dashboard</h2>
    <a onclick="showSection('products')">📦 Products</a>
    <a onclick="showSection('add')">➕ Add Product</a>
    <a onclick="showSection('orders')">🛒 Orders</a>
  </div>

  <!-- Content -->
  <div class="content">

    <!-- PRODUCTS -->
    <div id="products" class="admin-card">
      <h2>All Products</h2>
      <div id="product-list"></div>
    </div>

    <!-- ADD PRODUCT -->
    <div id="add" class="admin-card" style="display:none;">
      <h2>Add Product</h2>
      <input id="name" placeholder="Product Name">
      <input id="price" placeholder="Price">
      <input id="image" placeholder="Image path (photos/item.jpg)">
      <button onclick="addProduct()">Add Product</button>
    </div>

    <!-- ORDERS -->
    <div id="orders" class="admin-card" style="display:none;">
      <h2>Orders</h2>
      <p>Total Orders: <strong id="order-count">0</strong></p>
    </div>

  </div>

</div>

<script>

/* 🔐 SIMPLE PASSWORD */
let pass = prompt("Enter Admin Password");
if(pass !== "admin123"){
  alert("Access Denied");
  window.location.href = "index.html";
}

/* LOAD PRODUCTS */
let products = JSON.parse(localStorage.getItem("products")) || [];

/* DISPLAY PRODUCTS */
function displayProducts(){
  const list = document.getElementById("product-list");
  list.innerHTML = "";

  products.forEach((p,index)=>{
    list.innerHTML += `
      <div class="admin-product">
        <div style="display:flex; gap:15px; align-items:center;">
          <img src="${p.image}">
          <div>
            <h4>${p.name}</h4>
            <p>Rs ${p.price}</p>
          </div>
        </div>
        <button class="delete-btn" onclick="deleteProduct(${index})">Delete</button>
      </div>
    `;
  });
}

/* ADD PRODUCT */
function addProduct(){
  const name = document.getElementById("name").value;
  const price = document.getElementById("price").value;
  const image = document.getElementById("image").value;

  if(name === "" || price === "" || image === ""){
    alert("Fill all fields");
    return;
  }

  products.push({name, price:parseInt(price), image});
  localStorage.setItem("products", JSON.stringify(products));

  alert("Product Added");
  displayProducts();
}

/* DELETE */
function deleteProduct(index){
  products.splice(index,1);
  localStorage.setItem("products", JSON.stringify(products));
  displayProducts();
}

/* NAVIGATION */
function showSection(id){
  document.getElementById("products").style.display="none";
  document.getElementById("add").style.display="none";
  document.getElementById("orders").style.display="none";

  document.getElementById(id).style.display="block";
}

/* ORDERS */
function loadOrders(){
  let orders = JSON.parse(localStorage.getItem("orders")) || [];
  document.getElementById("order-count").innerText = orders.length;
}

/* INIT */
displayProducts();
loadOrders();

</script>

</body>
</html>