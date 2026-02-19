<?php
$config['protocol'] = env('EMAIL_PROTOCOL', 'smtp');

$config['smtp_host'] = env('EMAIL_SMTP_HOST', '');

$config['smtp_port'] = env('EMAIL_SMTP_PORT', '465');

$config['smtp_user'] = env('EMAIL_SMTP_USER', '');

$config['smtp_pass'] = env('EMAIL_SMTP_PASSWORD', '');

$config['mailtype'] = env('EMAIL_MAILTYPE', 'html');

$config['charset'] = env('EMAIL_CHARSET', 'iso-8859-1');

$config['wordwrap'] = env('EMAIL_WORDWRAP', TRUE);

$config['newline'] = env('EMAIL_NEWLINE', "\r\n");
//=============================================
