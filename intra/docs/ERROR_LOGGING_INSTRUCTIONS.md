# 📝 Error Logging System Instructions

## 🚀 How to Enable Error Logging

### Option 1: Quick Enable (Recommended)
Add this single line at the top of `main/index.php` (after the opening `<?php` tag):
```php
require_once(__DIR__ . '/../enable_error_logging.php');
```

### Option 2: Manual Include
Add this line at the top of `main/index.php`:
```php
require_once(__DIR__ . '/../error_logger.php');
```

## 📊 How to View Error Logs

### Web Interface (Recommended)
1. **Navigate to:** `http://local.eproc.intra.com/error_logger.php`
2. **Features available:**
   - View errors from last 1 hour, 6 hours, 24 hours, or 7 days
   - Color-coded error types (Fatal, Warning, Notice)
   - Auto-refresh every 30 seconds when errors are present
   - Clear logs functionality
   - File size and last modified information

### Manual File Access
- **Error logs:** `logs/php_errors_YYYY-MM-DD.log`
- **Access logs:** `logs/access_YYYY-MM-DD.log`

## 🔍 Error Types Captured

| Type | Description | Color |
|------|-------------|--------|
| **FATAL ERROR** | Critical errors that stop execution | Red |
| **WARNING** | Non-fatal errors that may cause issues | Orange |
| **NOTICE** | Minor issues and undefined variables | Blue |
| **DEPRECATED** | Use of deprecated functions | Gray |

## 📋 What Information is Logged

For each error, the system captures:
- **Timestamp** - When the error occurred
- **Error Type** - Severity level
- **Message** - Error description
- **File & Line** - Exact location of the error
- **URL** - Page where error occurred
- **IP Address** - User's IP
- **User Agent** - Browser information
- **Stack Trace** - Function call sequence

## 🛠️ Maintenance

### Automatic Features
- **Auto cleanup:** Logs older than 30 days are automatically deleted
- **File rotation:** New log file created each day
- **Performance tracking:** Execution time and memory usage logged

### Manual Actions
- **Clear logs:** Use the "Clear Logs" button in the web interface
- **Download logs:** Copy files from the `logs/` directory

## 🚨 Troubleshooting

### If Error Logging Doesn't Work
1. **Check file permissions:** Ensure the script can create the `logs/` directory
2. **Verify inclusion:** Make sure the require_once line is added correctly
3. **PHP configuration:** Ensure `log_errors` is enabled in PHP

### If No Errors Show
- This is good! It means no PHP errors are occurring
- You can test by temporarily adding an error like: `echo $undefined_variable;`

## 💡 Best Practices

1. **Monitor regularly:** Check the error log daily during development
2. **Fix warnings:** Don't ignore warnings, they can lead to bigger issues
3. **Performance monitoring:** Watch execution times and memory usage
4. **Clean production:** Disable error display in production while keeping logging

## 🎯 Quick Commands

```bash
# View recent error log
tail -f logs/php_errors_$(date +%Y-%m-%d).log

# Count errors today
grep -c "ERROR\|WARNING" logs/php_errors_$(date +%Y-%m-%d).log

# Clear today's logs
> logs/php_errors_$(date +%Y-%m-%d).log
```

---

**✅ Once enabled, this system will automatically detect and log all PHP errors, making debugging much faster and easier!** 