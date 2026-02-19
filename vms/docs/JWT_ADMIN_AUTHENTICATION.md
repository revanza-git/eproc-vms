# JWT Admin Authentication System

## Overview

This implementation adds JWT (JSON Web Token) authentication for admin users, automatically redirecting them to the intra domain (`local.eproc.web.com/internal`) with a secure token instead of keeping them on the local VMS application.

## How It Works

### 1. Login Flow for Admin Users

When an admin user logs in:

1. **Authentication**: User credentials are verified using the standard login process
2. **Session Creation**: Admin session is created with user data
3. **JWT Generation**: A JWT token is generated containing admin user information
4. **Redirect**: Admin is automatically redirected to `local.eproc.web.com/internal?jwt=<token>`

### 2. JWT Token Structure

The JWT token contains:

```json
{
  "iss": "http://local.eproc.web.com/app/",
  "iat": 1642678800,
  "exp": 1642682400,
  "sub": "user_id",
  "data": {
    "id_user": "123",
    "name": "Admin User",
    "id_sbu": "1",
    "id_role": "1",
    "role_name": "Administrator",
    "sbu_name": "Main Office",
    "app": "vms",
    "app_type": 1,
    "id_division": "1",
    "type": "admin"
  }
}
```

### 3. Configuration

Add these environment variables to your `.env` file:

```env
# Admin Intra Domain Configuration
ADMIN_INTRA_URL=http://local.eproc.web.com/internal
JWT_SECRET_KEY=your_secure_32_character_secret_key_here
JWT_ALGORITHM=HS256
JWT_EXPIRE_TIME=3600
```

## Security Features

- **Secure Token Generation**: Uses HMAC-SHA256 for token signing
- **Expiration Control**: Tokens expire after 1 hour (configurable)
- **Secure Secret**: Uses environment-based secret key configuration
- **Validation**: Includes token validation endpoint for verification

## API Endpoints

### Token Validation

**Endpoint**: `GET /main/validate_jwt`
**Parameters**: 
- `jwt` (required): The JWT token to validate

**Response**:
```json
{
  "success": true,
  "message": "Token is valid",
  "data": {
    "id_user": "123",
    "name": "Admin User",
    // ... other admin data
  }
}
```

## Implementation Details

### Files Modified/Created

1. **`app/application/libraries/Jwt_token.php`** - JWT token generation and validation library
2. **`app/application/modules/main/controllers/Main.php`** - Updated login flow for admin users
3. **`app/application/config/config.php`** - Added JWT configuration options
4. **`app/application/config/autoload.php`** - Auto-load JWT library
5. **`.env.example`** - Added environment variable examples

### Key Methods

- `Jwt_token::generate_admin_token($admin_data)` - Generate JWT for admin
- `Jwt_token::validate_token($token)` - Validate JWT token
- `Jwt_token::get_admin_redirect_url($admin_data)` - Get full redirect URL with token
- `Main::validate_jwt()` - Token validation endpoint

## Security Considerations

1. **Secret Key**: Ensure you use a strong, random 32+ character secret key in production
2. **HTTPS**: Use HTTPS in production to protect tokens in transit
3. **Token Expiration**: Tokens have a limited lifetime (1 hour by default)
4. **Domain Security**: Ensure the intra domain properly validates tokens

## Troubleshooting

### Common Issues

1. **"Failed to generate JWT token"**
   - Check JWT secret key configuration
   - Verify admin session data is complete
   - Check application logs for specific errors

2. **"Invalid or expired JWT token"**
   - Token may have expired (check JWT_EXPIRE_TIME setting)
   - Secret key mismatch between generation and validation
   - Token corruption during transmission

3. **Admin not redirecting to intra domain**
   - Verify ADMIN_INTRA_URL configuration
   - Check that admin session has `type` field set to 'admin'
   - Review application logs for redirect attempts

### Logging

The system logs JWT operations at various levels:
- **INFO**: Successful token generation and redirects
- **WARNING**: Token validation failures
- **ERROR**: Token generation failures and configuration issues

## Testing

To test the JWT authentication:

1. **Login as Admin**: Use admin credentials to log in
2. **Check Redirect**: Verify redirect to intra domain with JWT parameter
3. **Validate Token**: Use the `/main/validate_jwt` endpoint to verify token validity
4. **Check Logs**: Review application logs for JWT operations

## Fallback Behavior

If JWT token generation fails, the system will fall back to the original admin redirect logic:
- Role 6 admins → auction module
- App type 1 admins → pengadaan admin URL
- Other admins → standard admin dashboard
