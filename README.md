# typeofshit-portfolio

A premium editorial portfolio + project showcase web app built in PHP, MySQL, CSS, and JavaScript with secure login and role-based access control.

## Stack
- PHP 8+
- MySQL 8+
- HTML/CSS/JS
- PDO prepared statements
- Password hashing with `password_hash`
- Session-based authentication

## Features
- Public portfolio homepage with earthy editorial design
- Hidden admin login path
- Role-based access for Admin / Operations / Customer
- Portfolio item showcase with external URLs
- Activity log tracking and analytics hooks
- Protected admin and operations panels
- Responsive premium UI

## Project structure

```text
.
├── README.md
├── .htaccess
├── index.php
├── login.php
├── logout.php
├── admin/
│   └── index.php
├── operations/
│   └── index.php
├── customer/
│   └── index.php
├── api/
│   └── auth.php
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   └── animations.css
│   │   └── admin.css
│   ├── js/
│   │   └── main.js
│   ├── images/
│   │   └── noise.svg
│   └── fonts/ (optional)
├── includes/
│   ├── config.php
│   ├── database.php
│   ├── auth.php
│   ├── rbac.php
│   ├── security.php
│   └── functions.php
├── database/
│   └── schema.sql
├── config.example.php
└── uploads/
```

## Installation

1. Create MySQL database:

```sql
CREATE DATABASE typeofshit_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import schema:

```bash
mysql -u root -p typeofshit_portfolio < database/schema.sql
```

3. Copy config template:

```bash
cp config.example.php includes/config.php
```

4. Update database credentials in `includes/config.php`.

5. Start app:

```bash
php -S localhost:8000
```

Then open:
- http://localhost:8000/
- http://localhost:8000/login.php

## Default accounts
After importing the schema, the app creates an admin user:

- Email: `admin@typeofshit.com`
- Password: `AdminPass123!`

IMPORTANT: change it immediately after initial setup.

## Design notes
This app uses the exact design direction you provided:
- earthy palette with Forest/Sage/Olive/Cream/Moss
- Anton display headings and Inter utility type
- large rounded cards and blocks
- blurred nav glass effect
- animated reveal transitions using cubic-bezier(0.16, 1, 0.3, 1)
- noise texture overlay with subtle analog feel

## ZIP
This environment does not directly attach a ZIP file to the chat, but the repository is ready and can be downloaded from GitHub using the repository's Download ZIP option. The app source is already created in the repo.

## Security
- Password hashing with bcrypt
- CSRF tokens on forms
- Protected role checks
- Session-based access control
- Activity logging placeholders for analytics tracking

## License
MIT
