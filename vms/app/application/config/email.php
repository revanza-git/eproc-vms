<?php
// Load environment variables before using them
if (!function_exists('env')) {
    require_once(dirname(dirname(__FILE__)) . '/env_loader.php');
}

$config['protocol'] = env('EMAIL_PROTOCOL', 'smtp');

$config['smtp_host'] = env('EMAIL_SMTP_HOST', '');

$config['smtp_port'] = env('EMAIL_SMTP_PORT', '465');

$config['smtp_user'] = env('EMAIL_SMTP_USER', '');

$config['smtp_pass'] = env('EMAIL_SMTP_PASS', '');

$config['mailtype'] = env('EMAIL_MAILTYPE', 'html');

$config['charset'] = env('EMAIL_CHARSET', 'iso-8859-1');

$config['wordwrap'] = TRUE;

$config['newline'] = "\r\n";

// Email enabled/disabled flag for development
$config['email_enabled'] = env('EMAIL_ENABLED', 'false') === 'true';
//=============================================
