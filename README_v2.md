# typeofshit-portfolio v2

This repository has been upgraded to v2. The upgrade includes:
- Normalized roles table and user_roles join table
- Improved projects table with slugs and longer URL fields
- Activity logs and sessions improvements
- Migrations and setup_tokens tables for safe upgrades and first-run admin setup
- v2 SQL script (database/v2_schema.sql) intended to be pasted into Hostinger MySQL console

What I changed and why
- Roles are now a separate table for future flexibility and permission expansion.
- Users no longer rely on ENUM for role; this enables multiple roles per user.
- Projects include a slug for shareable URLs and longer url fields to avoid truncation.
- setup_tokens table implements a secure first-run admin password workflow so you can paste SQL into Hostinger without embedding bcrypt hashes.

Important notes before running SQL
- BACKUP your current database (export it) before running any upgrade SQL.
- The v2 SQL script attempts to be non-destructive but will DROP tables if they exist. If you need a migration path preserving old data, ask and I will provide step-by-step migration commands.

How to set the admin password after running v2 SQL on Hostinger
1. After pasting the SQL into Hostinger, query the setup token (if not returned automatically):
   SELECT token, expires_at FROM setup_tokens WHERE purpose='initial_admin_password' ORDER BY created_at DESC LIMIT 1;
2. Upload setup_admin.php (provided in /tools/setup_admin.php) to your webroot (remove after use).
3. Visit: https://your-site/setup_admin.php?token=<the_token>
4. Enter a strong password and submit. The script will hash the password (bcrypt cost 12) and store it.
5. Remove setup_admin.php from the server.

Files added in this commit
- database/v2_schema.sql (one-shot script)
- tools/setup_admin.php (web form to set initial admin password securely)
- README.md (v2 notes and instructions)
- DEVELOP.md (development guide)
- DEPLOY.md (deployment guide)

If you want a single SQL file that embeds a bcrypt hash for your admin password, generate the bcrypt hash locally and I will embed it for you and produce a download-ready SQL. However, for Hostinger, the setup token approach is safer and avoids embedding secrets in SQL.
