# User & Task Management API

A simple RESTful API built with **Laravel 11** and **MySQL** for managing users and tasks.

## Features

-   User management (Create, List, Show)
-   Task management (Create, List, Show, Update, Delete)
-   Pagination support for listing users and tasks
-   Input validation & error handling
-   API logging (`storage/logs/api.log`)
-   Unit & Feature tests using PHPUnit
-   SQLite support for testing

## Installation

1. Clone the repository:

```bash
git clone https://github.com/your-username/user-task-api.git
cd user-task-api
Install dependencies:


composer install
Copy .env.example to .env:


cp .env.example .env
Configure your database in .env:


DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
Run migrations:


php artisan migrate
Generate application key:


php artisan key:generate
Serve the application:


php artisan serve
API Endpoints
Users
POST /api/v1/users - Create a new user

GET /api/v1/users - List all users (supports ?per_page=)

GET /api/v1/users/{id} - Get a user by ID

Tasks
POST /api/v1/tasks - Create a new task

GET /api/v1/tasks - List all tasks (supports ?userId= & ?per_page=)

GET /api/v1/tasks/{id} - Get a task by ID

PUT /api/v1/tasks/{id} - Update a task

DELETE /api/v1/tasks/{id} - Delete a task

Logging
API requests and responses are logged to: storage/logs/api.log

Laravel default logs remain in: storage/logs/laravel.log

Testing
Uses SQLite in-memory database for tests

Run all tests:

cp .env.testing .env

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
DB_FOREIGN_KEYS=true

php artisan test




Run a specific test class:


php artisan test --filter=UserApiTest
php artisan test --filter=TaskApiTest
License
MIT

Postman Collection

You can import the Postman collection from:

postman/UserTaskAPI.postman_collection.json


This collection includes all routes for:

Users

Tasks

Example requests & responses

🧰 Tech Stack

Framework: Laravel 11

Database: MySQL / SQLite (for testing)

Language: PHP 8.2+

Testing: PHPUnit

Logging: Monolog custom channel


```
