# VenteDaily

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About VenteDaily

VenteDaily is a comprehensive inventory and retail management system built with Laravel. The application helps businesses manage their inventory across multiple locations, track sales and purchases, process returns, handle pre-orders, and maintain financial records.

## Key Features

- Multi-location inventory tracking
- Product categorization and variant management
- Sales and purchase management
- Returns and exchanges processing
- Financial tracking and reporting
- Export capabilities

## Documentation

For detailed documentation, please refer to the [VenteDaily Documentation](resources/markdown/documentation.md).

## Installation

```bash
# Clone the repository
git clone https://github.com/Ahnaffaiz/ventedaily.git
cd ventedaily

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Compile assets
npm run dev

# Start development server
php artisan serve
```

## License

VenteDaily is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
