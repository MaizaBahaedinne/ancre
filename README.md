# ancredeselites.tn

Application web de gestion de garderie scolaire basee sur Laravel 12.

## Stack

- PHP 8.3 cible (environnement actuel valide avec PHP 8.2.31)
- Laravel 12
- MySQL
- Blade + Bootstrap 5
- Laravel Breeze (authentification)
- Spatie Laravel Permission (roles et permissions)
- AdminLTE 3 (interface admin)

## Configuration locale

Fichier d'environnement principal:

- APP_NAME="ancredeselites.tn"
- APP_URL=http://localhost
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=ancredeselites
- DB_USERNAME=root
- DB_PASSWORD=

## Installation

1. Installer les dependances PHP:
	composer install
2. Installer les dependances frontend:
	npm install
3. Copier le fichier d'environnement:
	copy .env.example .env
4. Generer la cle application:
	php artisan key:generate
5. Creer la base MySQL `ancredeselites`.
6. Lancer les migrations:
	php artisan migrate

## Execution

Serveur Laravel:

- php artisan serve --host=127.0.0.1 --port=8000

Build frontend:

- npm run build

## Verification

Tests:

- php artisan test

Etat actuel du setup:

- tests OK (25/25)
- migrations OK
- auth Breeze OK
- Spatie Permission OK
- AdminLTE installe
