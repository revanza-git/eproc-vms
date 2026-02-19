# VMS to Main Project Login Flow - Complete Implementation

## Overview

This document describes the complete authentication flow when admin users log in to the VMS system and need to be redirected to the local.eproc.web.com/internal project.

## Login Flow Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────────┐
│   Admin User    │    │   VMS System     │    │   Main Project      │
│                 │    │ (local.eproc.    │    │ (local.eproc.intra. │
│                 │    │  vms.com)        │    │  com/main)          │
└─────────────────┘    └──────────────────┘    └─────────────────────┘
         │                       │                         │
         │ 1. Login Request       │                         │
         ├──────────────────────►│                         │
         │                       │                         │
         │                       │ 2. Validate Credentials │
         │                       │ ──────────────────────► │
         │                       │                         │
         │                       │ 3. Generate Key & Store │
         │                       │    Admin Data in JSON   │
         │                       │ ──────────────────────► │
         │                       │                         │
         │                       │ 4. Redirect with Key    │
         │                       │ ──────────────────────► │
         │                       │                         │
         │ 5. Redirect Response   │                         │
         │◄──────────────────────┤                         │
         │                       │                         │
         │ 6. GET /main/from_eks?key={key}                 │
         ├─────────────────────────────────────────────────►│
         │                                                 │
         │                       │ 7. Validate Key & Setup │
         │                       │    Session              │
         │                       │◄──────────────────────── │
         │                       │                         │
         │ 8. Redirect to Dashboard                        │
         │◄─────────────────────────────────────────────────┤
```

## Implementation Details

### 1. VMS System Implementation

When an admin user successfully logs in to the VMS system, implement the following:

#### A. Update Admin Login Logic

```php
// In your VMS admin login controller (e.g., app/application/modules/admin/controllers/Admin.php)
public function admin_login_process() {
    // ... existing login validation ...
    
    if ($login_successful && $user_is_admin) {
        $admin_data = $this->get_admin_session_data();
        
        // Generate authentication for main project
        $this->load->model('Main_model');
        $redirect_url = $this->main_model->generate_admin_auth_for_main($admin_data);
        
        if ($redirect_url) {
            // Redirect to main project
            redirect($redirect_url);
        } else {
            // Fallback to local admin dashboard
            redirect(site_url('admin/dashboard'));
        }
    }
}
```

#### B. Required Admin Data Structure

The VMS system should pass admin data in this format:

```php
$admin_data = array(
    'name'          => 'Admin Name',
    'id_user'       => 123,
    'id_role'       => 2,
    'id_division'   => 1,
    'email'         => 'admin@example.com',
    'photo_profile' => 'profile.jpg'
);
```

### 2. Main Project Implementation

The main project already has the `from_eks()` method implemented. Based on the actual implementation:

#### A. Endpoint Details

- **URL**: `http://local.eproc.web.com/internal/from_eks`
- **Method**: GET
- **Parameter**: `key` (string) - unique authentication key

#### B. Example Usage

```
http://local.eproc.web.com/internal/from_eks?key=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8
```

### 3. Database Requirements

Ensure the `ms_key_value` table exists in the `eproc` database:

```sql
-- Table structure (should already exist in eproc database)
CREATE TABLE `ms_key_value` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` text NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `key` (`key`),
    KEY `deleted_at` (`deleted_at`)
);
```

### 4. JSON Data Structure

The `value` field in `ms_key_value` table contains JSON with this structure:

```json
{
    "name": "Admin Name",
    "id_user": 123,
    "id_role": 2,
    "id_division": 1,
    "email": "admin@example.com",
    "photo_profile": "profile.jpg",
    "app_type": 2
}
```

## Security Features

### 1. Single-Use Keys
- Each authentication key can only be used once
- Keys are automatically invalidated after successful use

### 2. Key Expiration
- Keys expire after 15 minutes if not used
- Expired keys are automatically rejected

### 3. Input Validation
- All user data is validated and sanitized
- JSON structure is verified before processing

### 4. Session Security
- Previous sessions are cleared before setting new admin session
- Comprehensive logging for security auditing

### 5. Error Handling
- Secure error messages that don't reveal system details
- All authentication attempts are logged

## Error Scenarios

The system handles these error scenarios:

1. **Missing Key**: If no key parameter is provided
2. **Invalid Key**: If key doesn't exist in database
3. **Expired Key**: If key is older than 15 minutes
4. **Invalid JSON**: If stored data is corrupted
5. **Non-Admin User**: If user doesn't have admin privileges
6. **Database Errors**: Connection or query failures

## Testing the Implementation

### 1. Test Key Generation

```php
// Test in VMS system
$admin_data = array(
    'name' => 'Test Admin',
    'id_user' => 999,
    'id_role' => 2,
    'id_division' => 1,
    'email' => 'test@example.com'
);

$redirect_url = $this->generate_admin_auth_for_main($admin_data);
echo "Redirect URL: " . $redirect_url;
```

### 2. Test Authentication Flow

1. Access the VMS login page
2. Login with admin credentials
3. Verify automatic redirect to main project
4. Check that admin session is properly set up
5. Verify access to main project dashboard

### 3. Test Error Handling

- Try accessing `/main/from_eks` without key parameter
- Try using an invalid key
- Try using an expired key

## Logging and Monitoring

The system logs these events:

- **Successful authentications**: `from_eks: Successful external authentication for admin user: {id}`
- **Failed attempts**: `from_eks: Invalid or expired key: {key}`
- **System errors**: `from_eks: Exception occurred: {error_message}`

Monitor these logs for security and system health.

## Configuration Requirements

Ensure these URLs are properly configured:

- VMS Base URL: `http://local.eproc.web.com/app/`
- Main Project URL: `http://local.eproc.web.com/internal/`

## Summary

The complete login flow is properly configured with:

1. ✅ **VMS System**: `generate_admin_auth_for_main()` method generates secure keys
2. ✅ **Main Project**: `from_eks()` method already exists and handles authentication
3. ✅ **Security**: Single-use keys stored in `ms_key_value` table
4. ✅ **Database**: Both systems access the same `eproc` database
5. ✅ **Logging**: Comprehensive logging for debugging

The admin users will seamlessly transition from VMS (`local.eproc.web.com`) to the main project (`local.eproc.web.com/internal`) with secure authentication keys.
