# Environment Variable Configuration Guide

## Overview

The VMS eProc application now supports environment-based configuration using `.env` files. This allows for better security, flexibility, and deployment management by moving sensitive and environment-specific settings out of code files.

## Setup Instructions

### 1. Install Dependencies

First, ensure you have the required dependencies:

```bash
cd app/
composer install
```

This will install the `vlucas/phpdotenv` library required for environment variable loading.

### 2. Create Environment File

Copy the example environment file and customize it for your environment:

```bash
cp .env.example .env
```

Edit the `.env` file with your actual configuration values.

### 3. Configure Your Environment

Update the following sections in your `.env` file:

## Configuration Sections

### Application Environment

```env
ENVIRONMENT=production          # Environment mode: development, testing, production
DEBUG_MODE=false               # Enable/disable debug mode
```

### Database Configuration

#### Main Database
```env
DB_HOSTNAME=localhost          # Database server hostname
DB_PORT=3307                   # Database server port
DB_USERNAME=eproc_user         # Database username
DB_PASSWORD=your_password      # Database password (CHANGE THIS!)
DB_DATABASE_MAIN=eproc         # Main database name
DB_DRIVER=mysqli               # Database driver
```

#### Planning Database
```env
DB_DATABASE_PLANNING=eproc_perencanaan  # Planning database name
```

You can also override any main database setting for the planning database by prefixing with `DB_PLANNING_`:
- `DB_PLANNING_HOSTNAME`
- `DB_PLANNING_USERNAME`
- `DB_PLANNING_PASSWORD`
- etc.

### URL Configuration

```env
BASE_URL=https://your-domain.com/app/          # Main application URL
EXTERNAL_URL=https://your-domain.com/main      # External application URL
```

#### Authentication URLs (Optional)
If you need custom URLs for different modules:
```env
PENGADAAN_DASHBOARD_URL=https://your-domain.com/app/dashboard
PENGADAAN_ADMIN_URL=https://your-domain.com/app/admin
NUSANTARA_DASHBOARD_URL=https://your-domain.com/app/dashboard
NUSANTARA_BASE_URL=https://your-domain.com/app/
```

### Security Configuration

```env
ENCRYPTION_KEY=your_32_character_encryption_key_here  # MUST be 32 characters
```

**Important**: Generate a secure, random 32-character encryption key. You can use:
```bash
php -r "echo bin2hex(random_bytes(16));"
```

### Session Configuration

```env
SESSION_DRIVER=files                    # Session storage: files, database, redis
SESSION_COOKIE_NAME=app_eksternal       # Session cookie name
SESSION_EXPIRE=7200                     # Session expiration in seconds
SESSION_COOKIE_SECURE=true              # Require HTTPS for session cookies
SESSION_COOKIE_HTTPONLY=true            # Prevent JavaScript access to session cookies
```

## Environment-Specific Configurations

### Development Environment

```env
ENVIRONMENT=development
DEBUG_MODE=true
DB_HOSTNAME=localhost
DB_PORT=3307
BASE_URL=http://localhost:8080/app/
EXTERNAL_URL=http://localhost:8080/main
SESSION_COOKIE_SECURE=false
```

### Production Environment

```env
ENVIRONMENT=production
DEBUG_MODE=false
DB_HOSTNAME=prod-db-server.com
DB_PORT=3306
BASE_URL=https://eproc.company.com/app/
EXTERNAL_URL=https://eproc.company.com/main
SESSION_COOKIE_SECURE=true
SSL_ENABLED=true
```

## Security Best Practices

### 1. File Permissions

Ensure your `.env` file has proper permissions:

```bash
chmod 600 .env
```

### 2. Git Ignore

The `.env` file should NEVER be committed to version control. It's already included in `.gitignore`, but verify:

```bash
echo ".env" >> .gitignore
```

### 3. Backup Strategy

- Keep a backup of your production `.env` file in a secure location
- Document all custom environment variables used in your deployment

### 4. Environment Validation

The application includes fallback values for all environment variables, but you should validate that all required variables are set in production.

## Troubleshooting

### Environment Variables Not Loading

1. **Check composer dependencies**:
   ```bash
   composer install
   ```

2. **Verify .env file exists**:
   ```bash
   ls -la .env
   ```

3. **Check .env file syntax**:
   - No spaces around the equals sign: `KEY=value` (not `KEY = value`)
   - Use quotes for values with spaces: `KEY="value with spaces"`
   - No trailing spaces after values

### Database Connection Issues

1. **Verify database credentials** in `.env`
2. **Check database server accessibility**
3. **Ensure database exists**
4. **Test connection manually**:
   ```bash
   mysql -h DB_HOSTNAME -P DB_PORT -u DB_USERNAME -p DB_DATABASE_MAIN
   ```

### URL Configuration Issues

1. **Ensure URLs end with trailing slash** for BASE_URL
2. **Use HTTPS in production** with SSL_ENABLED=true
3. **Test URLs are accessible**

## Migration from Hardcoded Values

If you're migrating from the previous hardcoded configuration:

1. **Backup existing config files**
2. **Create .env file** with your current values
3. **Test thoroughly** in development environment
4. **Deploy with zero-downtime strategy**

## Environment Variables Reference

| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| `ENVIRONMENT` | string | `production` | Application environment |
| `DB_HOSTNAME` | string | `localhost` | Database hostname |
| `DB_PORT` | integer | `3307` | Database port |
| `DB_USERNAME` | string | `root` | Database username |
| `DB_PASSWORD` | string | - | Database password |
| `DB_DATABASE_MAIN` | string | `eproc` | Main database name |
| `DB_DATABASE_PLANNING` | string | `eproc_perencanaan` | Planning database name |
| `BASE_URL` | string | `http://local.eproc.web.com/app/` | Application base URL |
| `EXTERNAL_URL` | string | `http://local.eproc.web.com/main` | External URL |
| `ENCRYPTION_KEY` | string | `(required)` | Encryption key (32 chars) |
| `SESSION_DRIVER` | string | `files` | Session storage driver |
| `SESSION_EXPIRE` | integer | `7200` | Session expiration (seconds) |

## Support

For questions or issues with environment configuration:

1. **Check this documentation** first
2. **Verify .env file syntax** and permissions
3. **Test in development environment** before production
4. **Check application logs** for specific error messages

---

**Security Note**: Never share your `.env` file or commit it to version control. Always use secure, unique values for production environments.
