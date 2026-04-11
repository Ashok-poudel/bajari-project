const productContainer = document.getElementById("product-container");
const cartCountElement = document.getElementById("cart-count");
const cartItemsContainer = document.getElementById("cart-items");
const totalElement = document.getElementById("total");
const alertBox = document.getElementById("alert-box");
let products = [];

async function fetchJson(url, options = {}) {
  try {
    const response = await fetch(url, options);
    if (!response.ok) {
      return { success: false, message: 'Network error' };
    }
    return await response.json();
  } catch (error) {
    return { success: false, message: error.message };
  }
}

function showAlert(message) {
  if (!alertBox) {
    alert(message);
    return;
  }
  alertBox.innerText = message;
  alertBox.classList.add("show");
  setTimeout(() => alertBox.classList.remove("show"), 2000);
}

async function loadProducts() {
  const data = await fetchJson('products.php');
  if (data.success) {
    products = data.products;
    displayProducts();
  } else {
    showAlert(data.message || 'Unable to load products');
  }
}

function displayProducts() {
  if (!productContainer) return;
  productContainer.innerHTML = '';
  products.forEach(product => {
    productContainer.innerHTML += `
      <div class="product-card">
        <img src="${product.image}" alt="${product.name}">
        <div class="product-info">
          <h3>${product.name}</h3>
          <div class="price">Rs ${product.price}</div>
          <button onclick="addToCart(${product.id})">Add To Cart</button>
        </div>
      </div>
    `;
  });
}

async function addToCart(productId) {
  const data = await fetchJson('cart_api.php?action=add', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ product_id: productId, quantity: 1 })
  });
  if (data.success) {
    showAlert('Item added to cart');
    updateCartCount();
  } else {
    showAlert(data.message || 'Could not add item');
  }
}

async function updateCartCount() {
  if (!cartCountElement) return;
  const data = await fetchJson('cart_api.php?action=get');
  if (data.success) {
    cartCountElement.innerText = data.totalQty;
  }
}

async function loadCart() {
  if (!cartItemsContainer) return;
  const data = await fetchJson('cart_api.php?action=get');
  if (!data.success) {
    showAlert(data.message || 'Could not load cart');
    return;
  }

  cartItemsContainer.innerHTML = '';
  if (data.items.length === 0) {
    cartItemsContainer.innerHTML = `
      <div class="empty-cart">
        <div class="empty-cart-icon">🛒</div>
        <div class="empty-cart-message">Your cart is empty</div>
        <a href="index.php" class="shop-now-btn">Shop Now</a>
      </div>
    `;
  }

  data.items.forEach(item => {
    cartItemsContainer.innerHTML += `
      <div class="cart-item">
        <img src="${item.image}" alt="${item.name}">
        <div class="cart-item-details">
          <h3>${item.name}</h3>
          <div class="cart-item-price">Rs ${item.price}</div>
          <div class="quantity-controls">
            <button onclick="changeQty(${item.product_id}, -1)">-</button>
            <span>${item.quantity}</span>
            <button onclick="changeQty(${item.product_id}, 1)">+</button>
          </div>
          <button class="remove-btn" onclick="removeItem(${item.product_id})">Remove</button>
        </div>
      </div>
    `;
  });

  if (totalElement) {
    totalElement.innerText = data.total;
  }
}

async function changeQty(productId, change) {
  const data = await fetchJson('cart_api.php?action=update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ product_id: productId, change })
  });
  if (data.success) {
    loadCart();
    updateCartCount();
  } else {
    showAlert(data.message || 'Could not update cart');
  }
}

async function removeItem(productId) {
  const data = await fetchJson('cart_api.php?action=remove', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ product_id: productId })
  });
  if (data.success) {
    loadCart();
    updateCartCount();
  } else {
    showAlert(data.message || 'Could not remove item');
  }
}

async function proceedToCheckout() {
  const data = await fetchJson('cart_api.php?action=get');
  if (!data.success) {
    showAlert(data.message || 'Could not check cart');
    return;
  }
  if (data.totalQty === 0) {
    showAlert('Your cart is empty!');
    return;
  }
  window.location.href = 'checkout.html';
}

async function loadCheckoutTotal() {
  const amountElement = document.getElementById('total-amount');
  if (!amountElement) return;
  const data = await fetchJson('cart_api.php?action=get');
  if (data.success) {
    amountElement.innerText = data.total;
  }
}

if (productContainer) {
  loadProducts();
  updateCartCount();
}
if (cartItemsContainer) {
  loadCart();
  updateCartCount();
}
if (document.getElementById('total-amount')) {
  loadCheckoutTotal();
}

function searchProduct() {
  const input = document.getElementById('search');
  if (!input) return;
  const term = input.value.toLowerCase();
  const productCards = document.querySelectorAll('.product-card');
  productCards.forEach(card => {
    const name = card.querySelector('h3')?.innerText.toLowerCase() || '';
    card.style.display = name.includes(term) ? 'block' : 'none';
  });
}
