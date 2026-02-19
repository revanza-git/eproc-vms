<?php
/**
 * Root redirect for VMS eProc System
 * Redirects users to the unified app application
 * (Previously redirected to main - now consolidated into app)
 */

// Redirect to unified app application
header('Location: /app/');
exit();
?> 