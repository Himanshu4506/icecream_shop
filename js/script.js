// =====================================================
// Scoop Haven - Global JS
// =====================================================

document.addEventListener('DOMContentLoaded', function () {

  // Bootstrap form validation (client-side) for any form with class "needs-validation"
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });

  // Simple search filter for products (services page / order page flavor list)
  const searchBox = document.getElementById('flavorSearch');
  if (searchBox) {
    searchBox.addEventListener('keyup', function () {
      const term = searchBox.value.toLowerCase();
      document.querySelectorAll('.searchable-item').forEach(function (item) {
        const name = item.getAttribute('data-name').toLowerCase();
        item.style.display = name.includes(term) ? '' : 'none';
      });
    });
  }
});

// =====================================================
// ORDER FORM LOGIC (order.php)
// Handles flavor selection, quantity, live price calculation
// =====================================================
function initOrderForm(products) {
  const cart = {}; // productId -> {name, price, qty}

  window.toggleFlavor = function (id, name, price, checkboxEl) {
    const card = checkboxEl.closest('.flavor-select-item');
    const qtyWrapper = card.querySelector('.qty-wrapper');

    if (checkboxEl.checked) {
      card.classList.add('selected');
      qtyWrapper.classList.remove('d-none');
      cart[id] = { name: name, price: price, qty: parseInt(card.querySelector('.qty-input').value) || 1 };
    } else {
      card.classList.remove('selected');
      qtyWrapper.classList.add('d-none');
      delete cart[id];
    }
    renderSummary();
  };

  window.changeQty = function (id, inputEl) {
    let val = parseInt(inputEl.value);
    if (isNaN(val) || val < 1) val = 1;
    inputEl.value = val;
    if (cart[id]) {
      cart[id].qty = val;
      renderSummary();
    }
  };

  function renderSummary() {
    const list = document.getElementById('summaryList');
    const totalEl = document.getElementById('summaryTotal');
    const hiddenInput = document.getElementById('cartData');
    let total = 0;
    let html = '';
    let count = 0;

    for (const id in cart) {
      const item = cart[id];
      const subtotal = item.price * item.qty;
      total += subtotal;
      count++;
      html += `<div class="d-flex justify-content-between mb-2">
                  <span>${item.name} <small class="text-muted">x${item.qty}</small></span>
                  <span>₹${subtotal.toFixed(2)}</span>
                </div>`;
    }

    if (count === 0) {
      html = '<p class="text-muted mb-0">No flavors selected yet.</p>';
    }

    list.innerHTML = html;
    totalEl.textContent = '₹' + total.toFixed(2);
    hiddenInput.value = JSON.stringify(cart);

    const submitBtn = document.getElementById('submitOrderBtn');
    if (submitBtn) submitBtn.disabled = count === 0;
  }

  renderSummary();
}

// =====================================================
// Delivery type toggle (Pickup vs Home Delivery) on order.php
// =====================================================
function toggleDeliveryAddress() {
  const type = document.querySelector('input[name="delivery_type"]:checked');
  const addressBox = document.getElementById('addressBox');
  if (!type || !addressBox) return;
  if (type.value === 'Home Delivery') {
    addressBox.classList.remove('d-none');
    document.getElementById('delivery_address').setAttribute('required', 'required');
  } else {
    addressBox.classList.add('d-none');
    document.getElementById('delivery_address').removeAttribute('required');
  }
}
