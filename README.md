# AKAS POS - Modern Point of Sale System

This project is a modern Point of Sale (POS) system built with Laravel, designed for retail businesses. It aims to provide a robust, user-friendly, and efficient solution for managing sales, inventory, and user operations.

## Features

*   **User Authentication & Role Management:** Secure login/logout, role-based access control (Admin, Cashier), and user CRUD operations managed by administrators.
*   **Master Data Management:** Comprehensive management of products, categories, units, and suppliers.
*   **Sales Transactions:** Real-time POS interface for processing sales.
*   **Inventory Management:** Real-time stock updates and stock-in tracking.
*   **Shift Reconciliation:** Features for opening, closing shifts, and reconciling cash with blind counting.
*   **Audit Logging:** Immutable logs to track all significant actions within the system.

## Technology Stack

*   **Backend:** Laravel 11 (PHP 8.5)
*   **Database:** PostgreSQL 16
*   **Frontend:** Blade with Tailwind CSS
*   **Testing:** PHPUnit

## Getting Started

### Prerequisites

*   PHP 8.5+
*   Composer
*   Node.js and npm
*   PostgreSQL

### Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository_url>
    cd AKAS
    ```

2.  **Install Composer dependencies:**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```

4.  **Configure environment:**
    Copy `.env.example` to `.env` and set up your database credentials and application key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Update `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env` for your PostgreSQL connection.

5.  **Run migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```

6.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    Or run Vite for frontend assets:
    ```bash
    npm run dev
    ```

## Security

*   User passwords are securely hashed using bcrypt.
*   Role-based access control is enforced via Laravel Gates and Policies.
*   CSRF protection is enabled for web routes.

## Contributing

Contributions are welcome! Please refer to the CONTRIBUTING.md file for guidelines.

## License

This project is open-sourced software licensed under the MIT license.
