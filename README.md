# Online Computer Shop

Online Computer Shop is a PHP and MySQL e-commerce application with customer and administrator workflows. It uses an MVC-style folder layout, server-rendered PHP views, MySQLi prepared statements, JavaScript form behavior, and AJAX-powered product search.

## Links

- [Repository](https://github.com/romanrazan/Web_Tech_Project_Online_Computer_Shop)
- Live demo: no public deployment is currently available

## Main Features

- Customer registration, login, logout, profile updates, and remember-me support
- Product browsing by category and brand
- AJAX search and filtering with JSON responses
- Shopping-cart add, update, and remove workflows
- Checkout with recorded payment-method choices
- Customer order history and product reviews
- Administrator dashboards for categories, brands, products, customers, reviews, and orders
- CSRF protection, session-based authorization, escaped output, and prepared database statements

> Payment options such as Cash on Delivery, bKash, Nagad, and DBBL/Rocket are stored as order choices. The project does not integrate a real payment gateway.

## Technology Stack

| Area | Technology | Why it fits |
| --- | --- | --- |
| Server | PHP 8+ | Handles routing, sessions, validation, and server-rendered pages without a framework dependency |
| Database | MySQL with MySQLi | Provides relational storage, foreign keys, transactions, and prepared statements |
| Frontend | HTML5, CSS3, JavaScript | Keeps the interface lightweight and directly compatible with PHP views |
| Dynamic search | AJAX and JSON | Updates product results without a full page reload |
| Local runtime | Apache/MySQL through XAMPP or equivalent | Supplies the web server, PHP runtime, and database required by the application |

There is no `composer.json`, JavaScript package manifest, build pipeline, or automated test suite in this repository. The application runs directly through PHP.

## Project Structure

```text
Web_Tech_Project_Online_Computer_Shop/
├── api/                 # AJAX endpoints
├── config/              # MySQL connection and session/security helpers
├── controllers/         # Request and workflow handlers
├── database/            # Schema and sample-data SQL files
├── models/              # MySQL queries and data operations
├── public/
│   ├── css/             # Shared and responsive styles
│   ├── js/              # Client-side validation and AJAX behavior
│   └── uploads/         # Product and profile image storage
├── views/               # Customer, administrator, auth, and layout views
├── computer_shop.sql    # Additional database export
└── index.php            # Front controller and route switch
```

## Local Setup

### Prerequisites

- PHP 8.0 or newer with the `mysqli` extension
- MySQL 8 or MariaDB
- Apache (XAMPP is the simplest Windows option)
- Git

### 1. Clone the repository

```bash
git clone https://github.com/romanrazan/Web_Tech_Project_Online_Computer_Shop.git
cd Web_Tech_Project_Online_Computer_Shop
```

For XAMPP, place or clone the repository under `C:\xampp\htdocs\Web_Tech_Project_Online_Computer_Shop`.

### 2. Create and import the database

Start MySQL, open phpMyAdmin, and import:

```text
database/computer_shop_final_with_sample_data.sql
```

The script creates the `computer_shop` database, its tables, and local demonstration data. It drops the named project tables before recreating them, so use it only for a disposable local database.

For a schema without sample rows, use `database/computer_shop_schema_only_safe.sql`.

### 3. Configure the database connection

This project does not use environment variables. Update only the local values in `config/database.php` if they differ from the defaults:

```php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "computer_shop";
```

Do not commit real production credentials. For a deployed environment, move these values into server-managed environment variables or a private configuration file.

### 4. Run the application

With Apache and MySQL running, open:

```text
http://localhost/Web_Tech_Project_Online_Computer_Shop/
```

The PHP development server can also serve the project root when MySQL is already running:

```bash
php -S 127.0.0.1:8000
```

Then open `http://127.0.0.1:8000/`.

## Demo Accounts

The sample-data import creates these local-only accounts:

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin@shop.com` | `12345678` |
| Customer | `customer@shop.com` | `12345678` |

Change or remove demonstration accounts before any public deployment.

## Development and Verification

There is no compilation step. After changing PHP files, refresh the browser. To lint every PHP file in PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Manual verification should cover registration/login, product filtering and AJAX search, cart updates, checkout, reviews, order history, and each administrator page. No automated test command is currently included.
