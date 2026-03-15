# ?? TITLE: Contact Agenda - PHP Professional CRUD

A robust and secure Contact Management System built with PHP 8.1+ following professional software architecture patterns. This project demonstrates the implementation of a clean CRUD (Create, Read, Update, Delete) with a focus on security and scalability.

##  ?? KEY FEATURES: 

User Authentication: Secure login and registration system using password_hash.

Contact Management: Full CRUD operations for personal contacts.

### ?? Security First:

Protection against SQL Injection using PDO and Prepared Statements.

Environment variables for sensitive data via phpdotenv.

Secure folder structure (Public Directory pattern).

### ??? Architecture:

Singleton Pattern for efficient database connections.

Namespaces and PSR-4 Autoloading via Composer.

MVC-lite approach (Separation of concerns).

## ?? TECH STACK:

Language: PHP 8.1+

Database: MySQL / MariaDB

Dependency Management: Composer

Environment Management: PHP Dotenv

Version Control: Git and GitHub

## ?? INSTALLATION AND SETUP:

```Clone the repository:
git clone https://github.com/tu-usuario/Agenda.git
cd Agenda
```

Install dependencies: 

```composer install```

### ?? Database Configuration:

Create a database named agenda_app.

Import the schema from /database/schema.sql.

Create a .env file in the root directory and fill in your credentials:
DB_HOST=localhost
DB_NAME=agenda_app
DB_USER=root
DB_PASS=tu_password

Run the local server:
php -S localhost:8000 -t public

### ??? PROJECT STRUCTURE:

- src/: Core logic (Models, Database, Controllers).

- public/: Web root (index.php, CSS, JS). Only this folder is accessible to the web.

- database/: SQL scripts and migrations.

- vendor/: Third-party libraries (managed by Composer).

### **Developed whit ?? by Javier Lago Amoedo**