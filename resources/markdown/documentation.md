# VenteDaily - Inventory & Retail Management System

## Overview

VenteDaily is a comprehensive inventory and retail management system built with Laravel. The application helps businesses manage their inventory across multiple locations, track sales and purchases, process returns, handle pre-orders, and maintain financial records.

## System Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and NPM (for frontend assets)
- Laravel requirements (BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)

## Technology Stack

### Backend
- **Laravel**: PHP framework providing the foundation for the application
- **Jetstream**: Authentication scaffolding with team management capabilities
- **Sanctum**: API token authentication for API requests
- **Spatie Permission**: Role and permission management

### Frontend
- **Livewire**: Server-side rendering for reactive components without writing JavaScript
- **Alpine.js**: Lightweight JavaScript framework for adding interactivity
- **Tailwind CSS**: Utility-first CSS framework for flexible, responsive designs
- **Blade Templates**: Laravel's templating engine

### Key Packages
- **Laravel Excel/Maatwebsite**: For data import and export functionality
- **Jantinnerezo/Laravel-Livewire-Alert**: Toast notifications in Livewire
- **Barryvdh/Debugbar**: Debug and profiling tool
- **Spatie/Laravel-Permission**: Comprehensive role and permission management

## Key Features

### Inventory Management

- **Multi-location inventory tracking**: Track stock across different locations (home, store, etc.)
- **Product categorization**: Organize products by categories with previews
- **Product variants**: Manage products with different sizes and colors
- **Quality control**: Track items in QC process
- **Stock transfers**: Move inventory between locations

### Sales Management

- **Point of Sale (POS)**: Process sales transactions
- **Multiple payment types**: Support for various payment methods
- **Shipping tracking**: Monitor shipping status
- **Pre-orders**: Handle orders for out-of-stock items

### Purchase Management

- **Supplier management**: Maintain supplier information
- **Purchase tracking**: Record and track purchases
- **Payment terms**: Configure various payment terms

### Financial Management

- **Payment tracking**: Record payments for sales and purchases
- **Expense tracking**: Track and categorize expenses
- **Banking integration**: Connect with bank accounts
- **Export capabilities**: Export financial data for reporting

### Returns and Exchanges

- **Return processing**: Handle customer returns
- **Return reasons**: Track reasons for returns
- **Return status tracking**: Monitor the status of returns

### Reporting

- **Stock reports**: View current inventory levels
- **Sales reports**: Analyze sales performance
- **Purchase reports**: Review purchasing history
- **Export functionality**: Export data to various formats

## Project Structure

The application follows the standard Laravel MVC architecture:

- `app/Models`: Contains all database models
- `app/Http/Controllers`: Contains controllers that handle HTTP requests
- `app/Http/Livewire`: Contains Livewire components for reactive UIs
- `app/Enums`: Contains PHP enums for various statuses and types
- `app/Exports`: Contains export classes for data export functionality
- `database/migrations`: Contains database structure definitions
- `resources/views`: Contains Blade templates for the UI
- `resources/js`: Contains Alpine.js components and custom JavaScript
- `resources/css`: Contains Tailwind CSS configuration and custom styles

## User Roles and Permissions

VenteDaily implements a comprehensive role-based access control system using the Spatie Permission package. The system has the following predefined roles:

### Admin Role
Full access to all system features including:
- User management
- System settings configuration
- Access to all reports and data exports
- Permission management

### Sales Role
Access to sales-related features:
- Create and manage sales transactions
- Handle customer data
- Process returns
- Manage pre-orders
- Handle shipping
- Process withdrawals
- Manage online sales
- Handle expenses and costs related to sales

### Accounting Role
Access to financial features:
- View and generate financial reports
- Process payments
- Manage expenses
- Handle costs
- View sales and purchase data

### Warehouse Role
Access to inventory management features:
- Update stock levels
- Process stock transfers
- Handle quality control
- Manage returns
- View inventory reports

### User Role
Basic access with limited permissions:
- View assigned data
- Basic reporting features
- Limited transaction capabilities

### Permission System
The application uses a granular permission system where each action is controlled by specific permissions such as:
- Create/Read/Update/Delete access for each module
- Special permissions like "Manage Online Sales" or "Manage Update Stock"
- Report generation permissions

Permissions are assigned to roles, which are then assigned to users, creating a flexible access control system that can be tailored to specific business needs.

## Getting Started

### Installation

1. Clone the repository
   ```bash
   git clone https://github.com/Ahnaffaiz/ventedaily.git
   cd ventedaily
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Install JavaScript dependencies
   ```bash
   npm install
   ```

4. Set up environment file
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database settings in the `.env` file

6. Run database migrations
   ```bash
   php artisan migrate
   ```

7. Seed the database with default roles, permissions, and users
   ```bash
   php artisan db:seed
   ```

8. Compile assets
   ```bash
   npm run dev
   ```

9. Start the development server
   ```bash
   php artisan serve
   ```

### Default Users

After seeding the database, the following users will be available:

- **Admin**: admin@gmail.com (password: password)
- **Sales**: sales@gmail.com (password: password)
- **User**: user@gmail.com (password: password)
- **Warehouse**: warehouse@gmail.com (password: password)
- **Accounting**: accounting@gmail.com (password: password)

### Configuration

The application can be configured through various files in the `config/` directory:

- `config/app.php`: General application settings
- `config/database.php`: Database connection settings
- `config/filesystems.php`: File storage configuration
- `config/permission.php`: Role and permission settings

## Frontend Development

### Livewire Components

VenteDaily uses Livewire for server-side rendered reactive components. Key components include:

- User management components
- Sales and purchase forms
- Inventory management interfaces
- Role and permission management

To create a new Livewire component:

```bash
php artisan make:livewire ComponentName
```

### Alpine.js Integration

Alpine.js is used for client-side interactivity. It's integrated with Livewire components to provide enhanced user experiences. Key features used:

- Dropdown menus
- Modal dialogs
- Form validation
- Interactive tables

### Tailwind CSS

The application uses Tailwind CSS for styling. The configuration can be found in `tailwind.config.js`. Custom styles can be added in the `resources/css` directory.

To compile Tailwind CSS:

```bash
npm run dev # for development
npm run build # for production
```

## User Guide

### Managing Products

1. Navigate to the Products section
2. Add new products with details including name, description, category, etc.
3. Configure product variants (sizes, colors)
4. Set pricing information
5. Upload product images

### Managing Inventory

1. Navigate to the Inventory section
2. View current stock levels across all locations
3. Add or transfer stock between locations
4. Mark items for quality control
5. Process alterations ("vermak")

### Processing Sales

1. Navigate to the POS section
2. Select products and quantities
3. Apply discounts if applicable
4. Choose payment method
5. Process the transaction
6. Arrange shipping if required

### Managing User Roles and Permissions

1. Navigate to the User Management section (Admin only)
2. Create new users and assign roles
3. Modify existing role permissions if needed
4. View and manage existing users

### Handling Returns

1. Navigate to the Returns section
2. Enter the original sale information
3. Select items being returned
4. Record reason for return
5. Process refund or exchange

## API Documentation

VenteDaily provides an API for integration with other systems. API endpoints can be accessed through the `/api` route prefix and are protected with Laravel Sanctum authentication.

For detailed API documentation, refer to the API reference in the `routes/api.php` file.

## Troubleshooting

### Common Issues

- **Stock discrepancies**: Run the stock reconciliation tool from the admin dashboard
- **Payment processing errors**: Check payment gateway configuration in settings
- **Export failures**: Ensure proper file permissions on the export directory
- **Livewire component errors**: Clear view cache with `php artisan view:clear`
- **Permission issues**: Check user role assignments in the user management section

### Support

For additional support, please contact me Ahnaffaiz [faiz.putra553@gmail.com].
