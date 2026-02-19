# Environment Variables Configuration Guide

This guide explains how to configure the E-Procurement System using environment variables for different deployment environments.

## Overview

The E-Procurement System now uses environment variables for configuration management, making it easier to deploy across different environments (development, staging, production) without modifying code.

## Quick Setup

1. **Copy the environment template:**
   ```bash
   cp .env.example .env
   ```

2. **Edit the `.env` file** with your environment-specific values:
   ```bash
   nano .env
   # or use your preferred editor
   ```

3. **Ensure the `.env` file is not committed** to version control (it's already in `.gitignore`).

## Environment Variables Reference

### Application Environment
- `APP_ENV`: Set to `development`, `testing`, or `production`

### Database Configuration

#### Main Application Database
- `DB_DEFAULT_HOSTNAME`: Database host (default: 127.0.0.1)
- `DB_DEFAULT_PORT`: Database port (default: 3307)
- `DB_DEFAULT_USERNAME`: Database username (default: root)
- `DB_DEFAULT_PASSWORD`: Database password
- `DB_DEFAULT_DATABASE`: Database name (default: eproc_perencanaan)

#### Secondary Database (eproc)
- `DB_EPROC_HOSTNAME`: Database host
- `DB_EPROC_PORT`: Database port
- `DB_EPROC_USERNAME`: Database username
- `DB_EPROC_PASSWORD`: Database password
- `DB_EPROC_DATABASE`: Database name

#### Pengadaan Application Databases
- `DB_PENGADAAN_DEFAULT_*`: Configuration for pengadaan default database
- `DB_PENGADAAN_PERENCANAAN_*`: Configuration for pengadaan perencanaan database

### Application URLs

#### Main Application
- `MAIN_BASE_URL`: Base URL for main application
- `MAIN_PENGADAAN_URL`: Pengadaan module URL
- `MAIN_VMS_URL`: VMS system URL
- `MAIN_VMS_PENGADAAN_URL`: VMS pengadaan URL

#### Pengadaan Application
- `PENGADAAN_BASE_URL`: Base URL for pengadaan application
- `PENGADAAN_BASE_APP`: Base application URL
- `PENGADAAN_VMS_URL`: VMS URL for pengadaan
- `PENGADAAN_BASE_LINK_EXTERNAL`: External link for pengadaan
- `PENGADAAN_BASE_LINK`: Internal link for pengadaan

#### External/Production URLs
- `EXTERNAL_EPROC_URL`: External eproc URL for notifications
- `EXTERNAL_CRON_URL`: External cron URL
- `EXTERNAL_PENGADAAN_URL`: External pengadaan URL

### Email Configuration

#### Primary Email Configuration (Gmail)
- `EMAIL_PROTOCOL`: Email protocol (default: smtp)
- `EMAIL_SMTP_HOST`: SMTP host (default: tls://smtp.gmail.com)
- `EMAIL_SMTP_PORT`: SMTP port (default: 465)
- `EMAIL_SMTP_USER`: SMTP username
- `EMAIL_SMTP_PASSWORD`: SMTP password
- `EMAIL_MAILTYPE`: Mail type (default: html)
- `EMAIL_CHARSET`: Character set (default: iso-8859-1)
- `EMAIL_WORDWRAP`: Word wrap setting (default: TRUE)
- `EMAIL_NEWLINE`: Newline character (default: \r\n)

#### Alternative Email Configurations
- `EMAIL_ALT_*`: Alternative email server settings
- `EMAIL_PERTAMINA_*`: Pertamina email server settings

#### Email Addresses
- `EMAIL_FROM_ADDRESS`: Default from email address
- `EMAIL_FROM_NAME`: Default from name
- `EMAIL_VMS_FROM_ADDRESS`: VMS from email address
- `EMAIL_VMS_FROM_NAME`: VMS from name
- `EMAIL_BCC_ADDRESS`: BCC email address for notifications
- `EMAIL_TEST_ADDRESS`: Test email address for development
- `EMAIL_ADMIN_CC`: Admin CC addresses (comma-separated)

### File Paths and Storage
- `PHP_BINARY_PATH`: Path to PHP binary (Windows)
- `UPLOAD_PATH`: File upload directory
- `LAMPIRAN_PATH`: Attachment directory

### Security Configuration
- `ENCRYPTION_KEY`: Application encryption key
- `SESSION_ENCRYPTION_KEY`: Session encryption key

### Development/Testing Configuration
- `TEST_ADMIN_EMAIL`: Test admin email
- `TEST_ADMIN_PASSWORD`: Test admin password
- `DEBUG_MODE`: Debug mode setting
- `SHOW_DEBUG_BACKTRACE`: Show debug backtrace

## Environment-Specific Setup

### Development Environment

For local development, use the default values in `.env.example`. You may need to adjust:

```env
APP_ENV=development
DB_DEFAULT_HOSTNAME=127.0.0.1
DB_DEFAULT_PORT=3307
MAIN_BASE_URL=http://local.eproc.intra.com/main/
PENGADAAN_BASE_URL=http://local.eproc.intra.com/pengadaan/
DEBUG_MODE=TRUE
```

### Production Environment

For production deployment, ensure you:

1. **Set secure passwords:**
   ```env
   DB_DEFAULT_PASSWORD=your_secure_database_password
   EMAIL_SMTP_PASSWORD=your_email_password
   ENCRYPTION_KEY=generate_a_new_encryption_key
   ```

2. **Use production URLs:**
   ```env
   APP_ENV=production
   MAIN_BASE_URL=https://your-production-domain.com/main/
   PENGADAAN_BASE_URL=https://your-production-domain.com/pengadaan/
   ```

3. **Configure production email server:**
   ```env
   EMAIL_SMTP_HOST=your.mail.server.com
   EMAIL_SMTP_PORT=587
   EMAIL_SMTP_USER=your_production_email@company.com
   ```

4. **Disable debug mode:**
   ```env
   DEBUG_MODE=FALSE
   SHOW_DEBUG_BACKTRACE=FALSE
   ```

### Staging Environment

For staging, use production-like settings but with staging URLs and databases.

## File Structure

The environment system includes:

```
├── .env.example              # Environment template
├── .env                      # Your local environment (not in git)
├── main/application/helpers/env_helper.php    # Environment loader
├── pengadaan/application/helpers/env_helper.php    # Environment loader
└── ENVIRONMENT_SETUP.md      # This documentation
```

## Security Best Practices

1. **Never commit `.env` files** to version control
2. **Use strong, unique passwords** for each environment
3. **Regularly rotate encryption keys** and passwords
4. **Limit database access** to necessary hosts only
5. **Use HTTPS** in production environments
6. **Monitor email configuration** for unauthorized access

## Troubleshooting

### Environment Variables Not Loading

1. Check that the `.env` file exists in the project root
2. Verify the `env_helper.php` is loaded in `autoload.php`
3. Ensure file permissions allow reading the `.env` file
4. Check for syntax errors in the `.env` file

### Database Connection Issues

1. Verify database credentials in `.env`
2. Check if database server is accessible
3. Ensure database exists and user has proper permissions
4. Test connection with database client tools

### Email Configuration Issues

1. Verify SMTP credentials and server settings
2. Check firewall rules for SMTP ports
3. Test email configuration with simple test scripts
4. Review email server logs for authentication issues

### URL Configuration Issues

1. Ensure URLs end with trailing slashes where expected
2. Check web server configuration (virtual hosts, URL rewrite)
3. Verify SSL certificates for HTTPS URLs
4. Test URLs in browser to ensure accessibility

## Migration from Hardcoded Values

If you're upgrading from a version with hardcoded values:

1. **Create `.env` file** from the template
2. **Copy your current values** to the appropriate environment variables
3. **Test thoroughly** in development environment
4. **Deploy to staging** for additional testing
5. **Deploy to production** with production values

## Support

For questions or issues with environment configuration:

1. Check this documentation first
2. Review the `.env.example` file for reference values
3. Test with default values in development
4. Contact the development team for production deployment assistance

---

**Note:** This environment variable system improves security and deployment flexibility. Always test configuration changes in development before deploying to production. 