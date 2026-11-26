# Community Feedback and Suggestion Management System

PHP + MySQL web system (Bootstrap 5, Chart.js) for the Web Development final project.

## Folder Structure

```
feedback-system/
├── index.php                 Landing page
├── README.md
├── config/
│   ├── config.php            App name, BASE_URL, DB credentials, session start
│   └── database.php          PDO connection (db())
├── database/
│   └── schema.sql            Tables + seed categories + default admin
├── includes/
│   ├── functions.php         Helpers: auth guards, CSRF, validation, badges
│   ├── header.php            Navbar + flash messages
│   └── footer.php            Footer + scripts
├── auth/
│   ├── register.php
│   ├── login.php
│   └── logout.php
├── user/
│   ├── dashboard.php         My submissions + status counts
│   ├── submit.php            Create feedback
│   ├── edit.php              Edit (Pending only)
│   ├── delete.php            Delete (Pending only)
│   ├── view.php              Status + admin response
│   └── _form.php             Shared form partial
├── admin/
│   ├── dashboard.php         Stats + charts
│   ├── feedback.php          Search and filter all feedback
│   ├── review.php            Update status + respond
│   ├── delete.php
│   └── categories.php        Manage categories
├── assets/
│   ├── css/style.css
│   ├── js/main.js            Validation, confirms, counter
│   ├── js/charts.js          Dashboard charts
│   └── img/
└── docs/                     Put ERD, screenshots, documentation here
```

## Setup (XAMPP)

1. Copy the `feedback-system` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open phpMyAdmin, then Import `database/schema.sql`.
4. Open `http://localhost/feedback-system/`.
5. If the folder name differs or you use a MySQL password, edit `config/config.php`.

## Database (ERD)

`users (1) ──< feedback >── (1) categories`

## Features vs. Project Flow

| Flow step | File |
|---|---|
| Login / Registration | `auth/` |
| Submit + validation | `user/submit.php`, `includes/functions.php` |
| Save with status Pending | `user/submit.php` |
| Admin review, status, response | `admin/review.php` |
| User tracks status | `user/dashboard.php`, `user/view.php` |
| Search, filter | `admin/feedback.php` |
| Reports and charts | `admin/dashboard.php` |

## Security

Prepared statements (PDO), `password_hash`, CSRF tokens, output escaping, role-based access guards.
