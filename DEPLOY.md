# Deployment Guide - typeofshit-portfolio v2

This guide covers recommended steps to deploy the app to a production environment (Hostinger, DigitalOcean, AWS, etc.).

1) Provision server
- Use at least a t3.small / $5 droplet-like environment for low traffic.
- Install PHP 8.0+, MySQL 8+, and Nginx or Apache.

2) Secure server
- Create a non-root deploy user and lock down SSH.
- Configure firewall allowing only HTTP/HTTPS and SSH.
- Obtain an SSL certificate (Let's Encrypt).

3) Code deployment
- Clone the repository into /var/www/typeofshit-portfolio
- Set file permissions for webserver user (www-data/nginx/httpd)
- Copy includes/config.php with production credentials and secure SECRET_KEY.

4) Database
- Create the database and run database/v2_schema.sql in your production MySQL.
- Use the setup_admin.php workflow to set the initial admin password (remove the setup script after use).

5) Webserver config
- Nginx: configure site, point root to the project directory, enable try_files for pretty routing.
- Apache: ensure mod_rewrite is enabled and .htaccess is honored (AllowOverride All).
- Set proper PHP-FPM pool user to match file permissions.

6) Security & hardening
- Set APP_DEBUG = false
- Serve only over HTTPS and enable HSTS
- Use secure cookie flags (session.cookie_secure = 1) in production
- Limit access to includes/ and other private folders via webserver rules
- Use strong SECRET_KEY and rotate regularly

7) Backups & monitoring
- Schedule daily DB backups and keep 30 days of copies
- Monitor error logs and set up alerting for unusual activity
- Consider a CDN for assets and caching

8) Post-deploy
- Remove any setup scripts (tools/setup_admin.php)
- Create at least one admin user and verify RBAC and session behaviour
- Run security scans and penetration tests before going live


