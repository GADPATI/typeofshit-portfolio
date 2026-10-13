# Development Guide - typeofshit-portfolio v2

This guide helps you develop and test the project locally.

Prerequisites
- PHP 8.0+
- Composer (optional for future packages)
- MySQL 8+
- Node/npm (optional for frontend tooling)

Local setup
1. Clone the repo:
   git clone https://github.com/devendrakate/typeofshit-portfolio.git
   cd typeofshit-portfolio

2. Copy config.example.php to includes/config.php and update DB credentials:
   cp config.example.php includes/config.php
   # Edit includes/config.php to set DB_HOST/DB_USER/DB_PASS/DB_NAME and APP_URL

3. Import the v2 schema locally (recommended):
   mysql -u root -p < database/v2_schema.sql

4. Start a PHP built-in server for quick testing:
   php -S localhost:8000

5. If the setup token was created by the SQL, find it:
   SELECT token FROM setup_tokens WHERE purpose='initial_admin_password' ORDER BY created_at DESC LIMIT 1;

6. Upload tools/setup_admin.php to your webroot and visit:
   http://localhost:8000/tools/setup_admin.php?token=<token>

Security & development notes
- Use real HTTPS when deploying (self-signed for staging is okay).
- Use environment variables for secrets in production (don't commit credentials).
- Implement proper error handling and disable APP_DEBUG on production.

Testing
- Create test users and projects using the included API endpoints.
- Validate RBAC behaviour by assigning roles via user_roles table.

Common commands
- Run migrations (manual for now): apply SQL in database/migrations/ or use the migrations table to record runs.


