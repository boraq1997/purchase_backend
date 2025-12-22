<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

________________________________________________________________________________

🖥️ Purchase Management System Backend – Laravel 12

The Purchase Management System Backend is a RESTful API built with Laravel 12. It powers the Vue.js frontend of the system and handles all business logic, database operations, authentication, and reporting.

This backend manages the full purchasing workflow within Al-Abbas Holy Shrine, including user management, departments, purchase requests, approvals, inventory checks, and generating detailed reports.

🔹 Database Structure

The backend uses MySQL or any relational database supported by Laravel. Key tables:
	1.	users – store system users and their roles.
	2.	departments – departments that create purchase requests.
	3.	purchase_requests – main purchase requests submitted by departments.
	4.	request_items – individual items within a purchase request.
	5.	committees – committees responsible for approval, needs assessment, and warehouse checks.
	6.	committee_user – pivot table linking users to committees.
	7.	estimates – supplier quotations for requested items.
	8.	estimate_items – items within each estimate.
	9.	procurements – actual procurement records.
	10.	procurement_items – items within procurements.
	11.	warehouse_checks – inventory verification records.
	12.	needs_assessments – assessment of material necessity.
	13.	reports – generated reports for requests and procurement.

All relationships use proper foreign keys with cascadeOnDelete() or nullOnDelete() for data integrity.

🔹 Features
	•	Full RESTful API for Vue.js frontend.
	•	Role-based access control and permissions.
	•	Multi-step purchase request workflow: create → approve → assess → procure → report.
	•	Inventory management and warehouse validation.
	•	Supplier quotations and procurement tracking.
	•	Detailed report generation for each request.

  Installation & Setup

  # Clone the repository
git clone https://github.com/username/purchase-management-backend.git
cd purchase-management-backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start local development server
php artisan serve

The API will be available at http://127.0.0.1:8000/api

⸻

🔹 Contribution Guidelines
	1.	Open an issue for bugs or new features.
	2.	Fork the repository.
	3.	Create a new branch: git checkout -b feature/your-feature.
	4.	Submit a Pull Request for review.

⸻

🔹 License

This backend is intended for internal use by Al-Abbas Holy Shrine. Redistribution is prohibited without official permission.
