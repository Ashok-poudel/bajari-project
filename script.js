// ================= LOAD PRODUCTS =================
let products = JSON.parse(localStorage.getItem("products")) || [];

// First time default products (only if empty)
if(products.length === 0){
  products = [
    {name:"A4 Paper", price:200, image:"photos/A4paper.png"},
    {name:"Bed", price:12000, image:"photos/bed.jpg"},
    {name:"Bodycon Dress", price:1800, image:"photos/bodycon.jpg"},
    {name:"Book Rack", price:4000, image:"photos/book rack.jpg"},
    {name:"Book", price:500, image:"photos/book.jpg"},
    {name:"Coat", price:3500, image:"photos/coat.jpg"},
    {name:"Copy", price:100, image:"photos/copy.jpg"},
    {name:"Daraz Product", price:9999, image:"photos/daraz.jpg"},
    {name:"Daura Suruwal", price:2500, image:"photos/daurasurwal.jpg"},
    {name:"Formal Pant", price:1500, image:"photos/formalpant.jpg"},
    {name:"Harpic", price:300, image:"photos/harpic.jpg"},
    {name:"iPhone", price:95000, image:"photos/iphone.jpg"},
    {name:"Iron", price:2500, image:"photos/iron.jpg"},
    {name:"Karua", price:800, image:"photos/karuwa.jpg"},
    {name:"Kitchen Rack", price:6000, image:"photos/kitchen-Rack.jpg"},
    {name:"Laptop", price:85000, image:"photos/laptop.jpg"},
    {name:"Macbook", price:125000, image:"photos/macbook.jpg"},
    {name:"Mouse", price:900, image:"photos/mouse.jpg"},
    {name:"Pants", price:1200, image:"photos/pants.jpg"},
    {name:"Party Dress", price:3000, image:"photos/partydress.jpg"},
    {name:"Pen", price:40, image:"photos/pen.jpg"},
    {name:"Rabbit Toy", price:1200, image:"photos/rabbit.jpg"},
    {name:"Samsung Phone", price:70000, image:"photos/samsung.jpg"},
    {name:"Soap", price:120, image:"photos/soap.jpg"},
    {name:"Stainless Steel", price:2000, image:"photos/stainless steel.webp"},
    {name:"Tide", price:500, image:"photos/tide.jpg"},
    {name:"T-Shirt", price:700, image:"photos/tshirt.jpg"},
    {name:"Wooden Spoon", price:250, image:"photos/wooden-spoon.jpg"}
  ];

  localStorage.setItem("products", JSON.stringify(products));
}

// ================= DISPLAY PRODUCTS =================
const productContainer = document.getElementById("product-container");

if(productContainer){
  productContainer.innerHTML = "";

  products.forEach((product,index)=>{
    productContainer.innerHTML += `
      <div class="product-card">
        <img src="${product.image}" alt="${product.name}">
        <div class="product-info">
          <h3>${product.name}</h3>
          <div class="price">Rs ${product.price}</div>
          <button onclick="addToCart(${index})">Add To Cart</button>
        </div>
      </div>
    `;
  });
}

// ================= ADD TO CART =================
function addToCart(index){
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  const existingIndex = cart.findIndex(item => item.name === products[index].name);

  if(existingIndex !== -1){
    cart[existingIndex].quantity += 1;
  } else {
    cart.push({...products[index], quantity:1});
  }

  localStorage.setItem("cart", JSON.stringify(cart));

  showAlert("Item added to cart successfully");
  updateCartCount();
}

// ================= ALERT =================
function showAlert(message){
  const alertBox = document.getElementById("alert-box");

  if(alertBox){
    alertBox.style.display = "block";
    alertBox.innerText = message;

    setTimeout(()=>{
      alertBox.style.display = "none";
    },2000);
  }
}

// ================= CART COUNT =================
function updateCartCount(){
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let totalQty = 0;

  cart.forEach(item => totalQty += item.quantity);

  const count = document.getElementById("cart-count");
  if(count) count.innerText = totalQty;
}

updateCartCount();

// ================= DISPLAY CART =================
function displayCart(){
  const cartItems = document.getElementById("cart-items");
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;

  if(cartItems){
    cartItems.innerHTML = "";

    if(cart.length === 0){
      cartItems.innerHTML = "<p>Your cart is empty</p>";
    }

    cart.forEach((item,index)=>{
      total += item.price * item.quantity;

      cartItems.innerHTML += `
        <div class="cart-item">
          <img src="${item.image}">
          <div>
            <h3>${item.name}</h3>
            <p>Rs ${item.price}</p>

            <div class="quantity">
              <button onclick="changeQty(${index},-1)">-</button>
              <span>${item.quantity}</span>
              <button onclick="changeQty(${index},1)">+</button>
            </div>

            <button onclick="removeItem(${index})" class="remove-btn">Remove</button>
          </div>
        </div>
      `;
    });

    const totalElement = document.getElementById("total");
    if(totalElement) totalElement.innerText = total;
  }
}

// ================= CHANGE QUANTITY =================
function changeQty(index, change){
  let cart = JSON.parse(localStorage.getItem("cart"));

  cart[index].quantity += change;

  if(cart[index].quantity <= 0){
    cart.splice(index,1);
  }

  localStorage.setItem("cart", JSON.stringify(cart));

  updateCartCount();
  displayCart();
}

// ================= REMOVE ITEM =================
function removeItem(index){
  let cart = JSON.parse(localStorage.getItem("cart"));

  cart.splice(index,1);

  localStorage.setItem("cart", JSON.stringify(cart));

  updateCartCount();
  displayCart();
}

// ================= SAVE ORDER (IMPORTANT) =================
function saveOrder(){
  let orders = JSON.parse(localStorage.getItem("orders")) || [];
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  if(cart.length === 0) return;

  orders.push({
    items: cart,
    date: new Date().toLocaleString()
  });

  localStorage.setItem("orders", JSON.stringify(orders));
  localStorage.removeItem("cart");
}

// ================= LOAD CART =================
displayCart();

function searchProduct(){
  const input = document.getElementById("search").value.toLowerCase();
  const productCards = document.querySelectorAll(".product-card");

  productCards.forEach(card => {
    const name = card.querySelector("h3").innerText.toLowerCase();

    if(name.includes(input)){
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}