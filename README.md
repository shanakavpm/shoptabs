# ShopTabs PHP Project — Quick Install Guide

> **Note:** This project is built with **PHP 8.1** and does **not use any PHP framework**. All code is custom, plain PHP.

**1. Requirements:**
- Docker
- Git

**2. Setup:**
```bash
git clone <your-repo-url>
cd purephp
cp .env.example .env  # or edit .env directly
```

**3. Edit `.env`:**
- Set your DB and PayTabs info in `.env` file.

**4. Start with Docker:**
```bash
./run-ssl-docker.sh
```
- Open https://localhost/ or https://localhost:8443/
- Accept browser SSL warning

**5. Database:**
- Make sure MySQL is running and matches your `.env` settings.
- If needed, create DB:
```sql
CREATE DATABASE shoptabs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
- Run migration and factory scripts to set up tables and sample data:
```bash
php migrate.php
php factory.php
```

**6. Run tests (optional):**
```bash
composer install
composer test
```

**To stop:**
```bash
docker rm -f purephp-nginx-ssl-auto
```

That’s it! For issues, check your `.env` and Docker logs.
