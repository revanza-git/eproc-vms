# Complete Guide: Making Your E-Procurement Project Read .env Files

This guide provides step-by-step instructions to ensure your entire E-Procurement project can read and use environment variables from `.env` files.

## 📋 Quick Setup Checklist

- [ ] 1. Create `.env` file from template
- [ ] 2. Verify environment helpers are in place
- [ ] 3. Test environment variable loading
- [ ] 4. Configure web server (if needed)
- [ ] 5. Test both applications
- [ ] 6. Set up environment-specific configurations

---

## 🚀 Step 1: Create Your Environment File

### Copy the Template
```bash
# Navigate to your project root
cd /c:/inetpub/eproc/intra

# Copy the environment template
copy .env.example .env
```

### Edit Configuration
Open the `.env` file and configure it with your specific values:

```env
# Essential configurations to update:
DB_DEFAULT_PASSWORD=your_actual_database_password
MAIN_BASE_URL=http://your-domain.com/main/
PENGADAAN_BASE_URL=http://your-domain.com/pengadaan/
EMAIL_SMTP_PASSWORD=your_email_password
```

---

## 🔧 Step 2: Verify Environment System Components

### Check Helper Files Exist
Verify these files are present:

```
✅ main/application/helpers/env_helper.php
✅ pengadaan/application/helpers/env_helper.php
✅ .env.example (template)
✅ .env (your configuration)
```

### Check Autoload Configuration
Verify that both applications load the environment helper:

**File: `main/application/config/autoload.php`**
```php
$autoload['helper'] = array('env','form','url','file', 'utility','security');
```

**File: `pengadaan/application/config/autoload.php`**
```php
$autoload['helper'] = array('env','url','email','utility');
```

---

## 🧪 Step 3: Test Environment Loading

### Run the Test Script
1. Open your browser
2. Navigate to: `http://your-domain.com/test_env.php`
3. Review the test results

### Expected Results
✅ `.env` file found  
✅ Environment helper functions working  
✅ Key variables loaded  
✅ Database configuration loaded  
✅ URL configuration loaded  
✅ Email configuration loaded  

### If Tests Fail
Check the troubleshooting section below.

---

## 🌐 Step 4: Web Server Configuration (If Needed)

### For Apache Users
If using Apache, ensure `.htaccess` files are working:

**main/.htaccess:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

### For IIS Users
The `web.config` file is already configured. Ensure URL Rewrite module is installed.

### For Nginx Users
Add this to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

## 🔍 Step 5: Test Both Applications

### Test Main Application
1. Navigate to: `http://your-domain.com/main/`
2. Check that the application loads without errors
3. Test database connections
4. Test any email functionality

### Test Pengadaan Application
1. Navigate to: `http://your-domain.com/pengadaan/`
2. Check that the application loads without errors
3. Test database connections
4. Test any email functionality

### Verify Environment Variables in Use
Check that your applications are using environment variables by:

1. **Database Test**: Try connecting to admin panel or login
2. **URL Test**: Check that links between applications work
3. **Email Test**: Send a test email if possible

---

## 🔧 Step 6: Environment-Specific Setup

### Development Environment
```bash
# Use the .env file you created
# This is automatically loaded
```

### Staging Environment
```bash
# Create staging-specific environment
copy .env .env.staging

# Edit .env.staging with staging values
# Then rename when deploying:
# rename .env.staging .env
```

### Production Environment
```bash
# Create production-specific environment
copy .env .env.production

# Edit .env.production with production values:
# - Secure passwords
# - Production URLs
# - Production email settings
# - APP_ENV=production
# - DEBUG_MODE=FALSE
```

---

## 🚨 Troubleshooting Guide

### Issue: .env file not found
**Problem**: Test script shows ".env file not found"
**Solution**:
```bash
# Make sure you're in the project root
cd /c:/inetpub/eproc/intra

# Copy the template
copy .env.example .env

# Check file exists
dir .env
```

### Issue: Environment functions not available
**Problem**: Test shows "env() function not available"
**Solutions**:

1. **Check helper files exist**:
   ```bash
   # Verify files exist
   dir main\application\helpers\env_helper.php
   dir pengadaan\application\helpers\env_helper.php
   ```

2. **Check autoload configuration**:
   - Open `main/application/config/autoload.php`
   - Ensure `'env'` is in the helper array
   - Do the same for `pengadaan/application/config/autoload.php`

3. **Check file permissions**:
   - Ensure web server can read the files
   - On Windows, check file permissions

### Issue: Environment variables not loading
**Problem**: Variables show as "NOT_SET"
**Solutions**:

1. **Check .env file syntax**:
   ```env
   # Correct format:
   DB_HOST=localhost
   DB_PORT=3307
   
   # Incorrect format:
   DB_HOST = localhost    # No spaces around =
   DB_PORT="3307"         # No quotes needed for numbers
   ```

2. **Check .env file location**:
   - Must be in project root (`/c:/inetpub/eproc/intra/.env`)
   - Not in subdirectories

3. **Check for UTF-8 BOM**:
   - Save .env file without BOM
   - Use Notepad++ or similar editor

### Issue: Database connection fails
**Problem**: Applications can't connect to database
**Solutions**:

1. **Verify database variables**:
   ```env
   DB_DEFAULT_HOSTNAME=127.0.0.1
   DB_DEFAULT_PORT=3307
   DB_DEFAULT_USERNAME=root
   DB_DEFAULT_PASSWORD=your_password
   DB_DEFAULT_DATABASE=eproc_perencanaan
   ```

2. **Test database connection manually**:
   ```php
   // Test script
   $host = env('DB_DEFAULT_HOSTNAME');
   $port = env('DB_DEFAULT_PORT');
   $user = env('DB_DEFAULT_USERNAME');
   $pass = env('DB_DEFAULT_PASSWORD');
   $db = env('DB_DEFAULT_DATABASE');
   
   $connection = new mysqli($host, $user, $pass, $db, $port);
   if ($connection->connect_error) {
       die("Connection failed: " . $connection->connect_error);
   }
   echo "Connected successfully";
   ```

### Issue: URLs not working
**Problem**: Application URLs are incorrect
**Solutions**:

1. **Check URL variables**:
   ```env
   MAIN_BASE_URL=http://local.eproc.intra.com/main/
   PENGADAAN_BASE_URL=http://local.eproc.intra.com/pengadaan/
   # Ensure trailing slashes are present
   ```

2. **Update hosts file (if using local domains)**:
   ```
   # Add to C:\Windows\System32\drivers\etc\hosts
   127.0.0.1 local.eproc.intra.com
   127.0.0.1 local.eproc.vms.com
   ```

### Issue: Email not working
**Problem**: Email functionality fails
**Solutions**:

1. **Check email variables**:
   ```env
   EMAIL_SMTP_HOST=tls://smtp.gmail.com
   EMAIL_SMTP_PORT=465
   EMAIL_SMTP_USER=your-email@gmail.com
   EMAIL_SMTP_PASSWORD=your-app-password
   ```

2. **Test email configuration**:
   - Use Gmail App Passwords (not regular password)
   - Check firewall allows SMTP traffic
   - Test with telnet: `telnet smtp.gmail.com 587`

---

## 🔍 Verification Commands

### Check File Structure
```bash
# From project root, verify structure:
dir .env*
dir main\application\helpers\env_helper.php
dir pengadaan\application\helpers\env_helper.php
dir main\application\config\autoload.php
dir pengadaan\application\config\autoload.php
```

### Test Environment Loading
```bash
# Run the test script in browser:
# http://your-domain.com/test_env.php
```

### Check Log Files
```bash
# Check for errors in:
dir main\application\logs\
dir pengadaan\application\logs\
```

---

## 📚 Advanced Configuration

### Multiple Environment Files
You can create environment-specific files:

```bash
.env.example        # Template
.env                # Current environment
.env.development    # Development settings
.env.staging        # Staging settings
.env.production     # Production settings
```

### Environment Variable Precedence
Variables are loaded in this order:
1. System environment variables
2. `.env` file variables
3. Default values in code

### Security Best Practices
1. **Never commit `.env` files** (already in `.gitignore`)
2. **Use strong passwords** in production
3. **Limit file permissions** on `.env` files
4. **Rotate credentials** regularly
5. **Use HTTPS** in production

---

## 🎯 Final Verification Steps

1. **✅ Test Script Passes**: All sections show green checkmarks
2. **✅ Main App Works**: Login and basic functionality
3. **✅ Pengadaan App Works**: Login and basic functionality
4. **✅ Database Connected**: No connection errors
5. **✅ Email Configured**: Test email sending
6. **✅ URLs Correct**: Navigation between apps works
7. **✅ Cron Jobs Work**: If using scheduled tasks

---

## 📞 Getting Help

If you encounter issues:

1. **Check this guide** first
2. **Run the test script** (`test_env.php`)
3. **Check error logs** in application/logs/
4. **Verify file permissions** and paths
5. **Test with default values** to isolate issues

---

## 🎉 Success!

Once everything is working:

1. **Delete test_env.php** (security)
2. **Document your configuration** for team members
3. **Create environment-specific configs** for deployment
4. **Set up automated deployment** processes
5. **Monitor for configuration issues** in production

Your E-Procurement system is now properly configured with environment variables! 🚀 