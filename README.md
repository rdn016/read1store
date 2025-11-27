# Read1one Camera Store

A comprehensive e-commerce admin system for a e-commerce store built with Laravel 12 and Filament 3.2.

## Features

- **Product Management**: Manage camera products with specifications, images, and stock tracking
- **Order Processing**: Handle customer orders with WhatsApp integration and status management
- **Admin Dashboard**: Interactive dashboard with charts, latest orders, and low stock alerts
- **User Management**: Role-based permissions using Filament Shield
- **Category Organization**: Organize products into categories
- **Stock Management**: Automatic stock updates on order confirmations/cancellations

## Setup

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL or compatible database

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/rdn016/read1store.git
   cd read1store
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Copy environment file and configure:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database in `.env` file

6. Run migrations and seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. Link storage:
   ```bash
   php artisan storage:link
   ```

8. Generate Filament Shield permissions:
   ```bash
   php artisan shield:generate
   ```

9. Start the development server:
    ```bash
    php artisan serve
    ```

### Access the Website

- URL: `http://localhost:8000`
- Admin URL: `http://localhost:8000/atmint`


## Usage

- **Dashboard**: View statistics, latest orders, and low stock alerts
- **Products**: Add/edit products with specifications and images
- **Orders**: Manage orders, update status, and contact customers via WhatsApp
- **Categories**: Organize products into categories
- **Users**: Manage admin users and permissions

## Technologies Used

- **Laravel 11**: PHP framework
- **Filament 3.3**: Admin panel builder
- **Filament Shield**: Role-based permissions
- **MySQL**: Database
- **Tailwind CSS**: Styling
- **Vite**: Asset bundling

## License

This project is licensed under the MIT License.