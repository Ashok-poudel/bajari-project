const products = [
  {name:"A4 Paper", price:200, image:"photos/A4paper.png"},
  {name:"Bed", price:12000, image:"photos/bed.jpg"},
  {name:"Bodycon Dress", price:1800, image:"photos/bodycon.jpg"},
  {name:"Book", price:500, image:"photos/book.jpg"},
  {name:"Coat", price:3500, image:"photos/coat.jpg"},
  {name:"iPhone", price:95000, image:"photos/iphone.jpg"},
  {name:"Laptop", price:85000, image:"photos/laptop.jpg"},
  {name:"Macbook", price:125000, image:"photos/macbook.jpg"},
  {name:"Mouse", price:900, image:"photos/mouse.jpg"},
  {name:"Pen", price:40, image:"photos/pen.jpg"},
  {name:"T-Shirt", price:700, image:"photos/tshirt.jpg"},
  {name:"Rabbit Toy", price:1200, image:"photos/rabbit.jpg"},
  {name:"Soap", price:120, image:"photos/soap.jpg"},
  {name:"Samsung Phone", price:70000, image:"photos/samsung.jpg"},
  {name:"Wooden Spoon", price:250, image:"photos/wooden-spoon.jpg"}
];

const productContainer = document.getElementById("product-container");
if(productContainer){
  products.forEach((product,index)=>{
    productContainer.innerHTML += `
      <div class="product-card">
        <img src="${product.image}">
        <div class="product-info">
          <h3>${product.name}</h3>
          <div class="price">Rs ${product.price}</div>
          <button onclick="addToCart(${index})">Add To Cart</button>
        </div>
      </div>`;
  });
}

function addToCart(index){
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.push({...products[index], quantity:1});
  localStorage.setItem("cart", JSON.stringify(cart));
  const alertBox = document.getElementById("alert-box");
  if(alertBox){
    alertBox.style.display = "block";
    alertBox.innerText = "Item added to cart successfully";
    setTimeout(()=>{
      alertBox.style.display = "none";
    },2000);
  }
  updateCartCount();
}

function updateCartCount(){
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const count = document.getElementById("cart-count");
  if(count) count.innerText = cart.length;
}

updateCartCount();

const cartItems = document.getElementById("cart-items");
if(cartItems){
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;
  cartItems.innerHTML = ""; // clear previous content
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
        </div>
      </div>`;
  });
  const totalElement = document.getElementById("total");
  if(totalElement) totalElement.innerText = total;
}

function changeQty(index,change){
  let cart = JSON.parse(localStorage.getItem("cart"));
  cart[index].quantity += change;
  if(cart[index].quantity <= 0){
    cart.splice(index,1);
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  location.reload(); // simple solution
}