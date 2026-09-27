# 🍦 Scoop Haven – Ice Cream Shop Management System

A complete Ice Cream Shop website built with **PHP, MySQL, HTML, CSS, JavaScript, Bootstrap 5**, designed to run on **Apache via XAMPP**.

---

## 1. Features

**Public Website**
- Home page with featured products, testimonials, contact info
- About Us page (mission, vision, history, team)
- Services page (flavors/categories, custom orders, catering, delivery) with search & category filter
- Order page: flavor selection, quantity, live price calculation, customer details form, order confirmation
- Delivery tracking page: track by Order ID, visual status tracker, estimated delivery time

**Admin Panel** (`/admin`)
- Secure login (hashed passwords, session-based auth)
- Dashboard with stats (products, orders, customers, revenue)
- Manage Products: Add / Edit / Delete, image upload, search
- Manage Orders: search/filter, view details, update delivery status (with history log)
- View Customers and their order totals

**Technical**
- Responsive design (Bootstrap 5, mobile-friendly)
- Client + server-side form validation
- PHP sessions for admin auth
- Prepared statements (mysqli) throughout — SQL-injection safe
- Search functionality on Services, Products, Orders, Customers

---

## 2. Folder Structure

```
icecream_shop/
├── index.php                  # Home page
├── about.php                  # About Us page
├── services.php                # Services / flavor menu page
├── order.php                  # Order form page
├── process_order.php           # Handles order submission (server-side)
├── order_confirmation.php      # Order confirmation / receipt
├── track_order.php             # Delivery status tracking page
├── includes/
│   ├── db.php                  # MySQL connection
│   ├── header.php               # Shared site header/navbar
│   └── footer.php               # Shared site footer
├── css/
│   └── style.css               # Main stylesheet
├── js/
│   └── script.js               # Global JS (validation, order form logic)
├── uploads/
│   └── products/               # Uploaded product images
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── products.php
│   ├── add_product.php
│   ├── edit_product.php
│   ├── delete_product.php
│   ├── orders.php
│   ├── order_details.php
│   ├── update_status.php
│   ├── customers.php
│   └── includes/
│       ├── auth_check.php
│       ├── admin_header.php
│       └── admin_footer.php
├── database/
│   └── icecream_shop.sql        # Full database schema + seed data
└── README.md
```

---

## 3. Database Tables

| Table            | Purpose                                                  |
|-------------------|-----------------------------------------------------------|
| `admin`           | Admin login accounts                                     |
| `customers`       | Customer details captured at checkout                    |
| `products`        | Ice cream flavors/products                                |
| `orders`          | Order header (totals, delivery type, status)              |
| `order_items`     | Line items per order (flavor, qty, price)                 |
| `delivery_status` | Status history/log per order (used by the tracker)        |
| `testimonials`    | Customer testimonials shown on the home page               |

---

## 4. Setup Instructions (XAMPP)

### Step 1 — Install XAMPP
Download and install XAMPP from https://www.apachefriends.org (includes Apache + MySQL + PHP).

### Step 2 — Copy the project
Copy the entire `icecream_shop` folder into your XAMPP `htdocs` directory:
- Windows: `C:\xampp\htdocs\icecream_shop`
- macOS: `/Applications/XAMPP/htdocs/icecream_shop`
- Linux: `/opt/lampp/htdocs/icecream_shop`

### Step 3 — Start Apache & MySQL
Open the **XAMPP Control Panel** and click **Start** next to both **Apache** and **MySQL**.

### Step 4 — Create the database
1. Open `http://localhost/phpmyadmin` in your browser.
2. Click **Import**.
3. Choose the file `database/icecream_shop.sql` from this project.
4. Click **Go**. This creates the `icecream_shop` database with all tables and sample data.

> Alternatively, from a terminal:
> ```
> mysql -u root -p < database/icecream_shop.sql
> ```

### Step 5 — Configure database connection (if needed)
Open `includes/db.php`. Default XAMPP settings (root user, no password) are already configured:
```php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'icecream_shop';
```
Update these only if your MySQL setup differs.

### Step 6 — Set upload folder permissions
Make sure `uploads/products/` is writable so the admin panel can upload product images (usually fine by default on Windows/macOS; on Linux run `chmod -R 755 uploads/`).

### Step 7 — Open the site
- Public website: `http://localhost/icecream_shop/`
- Admin panel: `http://localhost/icecream_shop/admin/login.php`

### Default Admin Login
```
Username: admin
Password: admin123
```
**Important:** Change this password (or add a new admin row with a new bcrypt hash) before using this in any real/production environment.

---

## 5. How Ordering & Tracking Works

1. A customer visits **Order Now**, selects one or more flavors, sets quantities, and the total is calculated live in JavaScript.
2. On submit, `process_order.php` re-validates all prices server-side (never trusts client-submitted prices), creates a `customers` row, an `orders` row with a generated Order ID (e.g. `SH260713XXXXX`), `order_items` rows, and an initial `delivery_status` row.
3. The customer is shown an **Order Confirmation** page with their Order ID.
4. The customer can visit **Track Order** and enter that Order ID at any time to see the current status and full history.
5. In the **Admin Panel → Manage Orders**, staff can open any order and update its status (Order Placed → Preparing → Out for Delivery → Delivered, or Cancelled). Every update is logged to `delivery_status` and instantly reflected on the customer's tracking page.

---

## 6. Notes

- Product images are optional — if none is uploaded, a styled ice cream icon placeholder is shown automatically, so the site looks complete even without image assets.
- All forms include both client-side (HTML5 + Bootstrap) and server-side validation.
- Passwords are stored using PHP's `password_hash()` / verified with `password_verify()` (bcrypt).
- All database queries use prepared statements to prevent SQL injection.
