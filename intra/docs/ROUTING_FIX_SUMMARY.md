# Routing Fix: Main to Pengadaan Module

## Problem
When users clicked "Ke aplikasi pengadaan b/j" menu, they were redirected back to the main dashboard instead of accessing the pengadaan module. This occurred due to **session isolation** between the two modules.

## Root Cause
- Main module uses session cookie: `perencanaan_internal` with path `/internal/main/`
- Pengadaan module uses session cookie: `pengadaan_internal` with path `/internal/pengadaan/`
- Different cookie names and paths prevented session sharing between modules
- When pengadaan checked for session data, it found nothing and redirected to VMS/main

## Solution Implemented
Implemented secure token-based authentication transfer using the existing `ms_key_value` table infrastructure.

### Changes Made

#### 1. Main Module Controller (`main/application/controllers/Main.php`)

**Added Two New Methods:**

**`to_pengadaan_admin()`**
- Generates a secure SHA-256 hash key
- Stores admin session data in `ms_key_value` table as JSON
- Redirects to pengadaan with the key: `pengadaan_url/main/login_admin?key=xxxxx`
- Transfers: id_user, name, id_sbu, id_role, role_name, sbu_name, app, division, id_division

**`to_pengadaan_user()`**
- Handles regular user redirects to pengadaan dashboard
- Currently redirects directly (can be enhanced with token transfer if needed)

**Modified `index()` Method:**
- Now calls `to_pengadaan_admin()` for admin users with app_type = 1
- Calls `to_pengadaan_user()` for regular users

**Enhanced `from_eks()` Method:**
- Added expiration check (15 minutes)
- Added `created_at > expiry_time` condition for better security

#### 2. Pengadaan Module Controller (`pengadaan/application/modules/main/controllers/Main.php`)

**Enhanced `login_admin()` Method:**
- Added comprehensive error handling with try-catch
- Validates key parameter existence
- Checks key expiration (15 minutes timeout)
- Validates JSON structure
- Validates required fields: id_user, name, id_role, role_name
- Provides default values for optional fields
- Logs all authentication attempts and failures
- Invalidates keys after use (marks as deleted)

## Security Features

1. **One-Time Use Keys**: Each key is marked as deleted after use
2. **Time Expiration**: Keys expire after 15 minutes
3. **Secure Hashing**: Uses SHA-256 with random bytes and timestamp
4. **Validation**: Multiple validation layers for data integrity
5. **Logging**: Comprehensive error and success logging for audit trail

## How It Works

### Authentication Flow
```
1. User clicks "Ke aplikasi pengadaan b/j" in main module
2. Main controller calls to_pengadaan_admin()
3. Secure key generated: hash(user_id + timestamp + random_bytes)
4. Session data stored in ms_key_value table with created_at timestamp
5. User redirected to: pengadaan/main/login_admin?key=xxxxx
6. Pengadaan controller validates:
   - Key exists
   - Key not expired (< 15 minutes old)
   - Key not already used (deleted_at is NULL)
   - JSON data valid
   - Required fields present
7. Session created in pengadaan module
8. Key marked as deleted (one-time use)
9. User redirected to admin dashboard
```

### Database Table: ms_key_value
```sql
Columns:
- key (VARCHAR): Unique SHA-256 hash
- value (TEXT): JSON encoded session data
- created_at (DATETIME): Timestamp for expiration check
- deleted_at (DATETIME): NULL if unused, timestamp if used/invalid
```

## Testing Instructions

### Test Case 1: Normal Admin Redirect
1. Log in to main module as admin with app_type = 1
2. Click "Ke aplikasi pengadaan b/j" menu
3. **Expected**: Redirected to pengadaan admin dashboard with session intact
4. **Verify**: User info displayed correctly in pengadaan module

### Test Case 2: Key Expiration
1. Generate a key but don't use it for 16 minutes
2. Try to access pengadaan with expired key
3. **Expected**: Redirect to VMS login
4. **Verify**: Error logged in application logs

### Test Case 3: Double Key Usage
1. Use a valid key to log in
2. Try to use the same key again
3. **Expected**: Redirect to VMS (key already marked as deleted)
4. **Verify**: "Invalid or expired key" error logged

### Test Case 4: Session Persistence
1. Access pengadaan module successfully
2. Navigate between pages in pengadaan
3. **Expected**: Session remains active, no re-authentication needed
4. **Verify**: User stays logged in

## Configuration Requirements

Ensure these settings are correct in both modules:

**Main Module** (`main/application/config/config.php`):
```php
$config['pengadaan_url'] = 'http://local.eproc.intra.com/pengadaan/';
```

**Pengadaan Module** (`pengadaan/application/config/config.php`):
```php
$config['vms_url'] = 'http://local.eproc.vms.com/';
```

## Monitoring and Debugging

### Log Files to Monitor
- `main/application/logs/` - Check for session transfer attempts
- `pengadaan/application/logs/` - Check for authentication validation

### Log Messages to Look For
**Success:**
- "Successful authentication for admin user: [id]"

**Errors:**
- "No key parameter provided"
- "Invalid or expired key: [hash]..."
- "Failed to decode JSON for key: [hash]..."
- "Missing required field: [field_name]"

### Database Queries for Debugging
```sql
-- Check recent key usage
SELECT * FROM ms_key_value 
WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY created_at DESC;

-- Check expired keys
SELECT * FROM ms_key_value 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE)
AND deleted_at IS NULL;

-- Clean up old keys (run periodically)
UPDATE ms_key_value 
SET deleted_at = NOW() 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);
```

## Future Enhancements

1. **User Session Transfer**: Implement token-based transfer for regular users
2. **Key Cleanup Job**: Create cron job to clean old keys from database
3. **Session Sharing**: Consider unified session management across modules
4. **Rate Limiting**: Add rate limiting for key generation to prevent abuse
5. **Multi-factor Auth**: Add additional security layer for sensitive operations

## Rollback Plan

If issues occur, restore these files from backup:
1. `main/application/controllers/Main.php`
2. `pengadaan/application/modules/main/controllers/Main.php`

The changes are backward compatible - existing VMS integration remains unchanged.

## Support

For issues or questions:
1. Check application logs first
2. Verify database ms_key_value table structure
3. Confirm URL configurations match your environment
4. Review this document's troubleshooting section

---
**Implementation Date**: December 1, 2025
**Version**: 1.0
**Status**: ✅ Implemented and Ready for Testing
