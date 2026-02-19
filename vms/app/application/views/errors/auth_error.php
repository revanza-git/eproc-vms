<!DOCTYPE html>
<html lang="en">
<head>
    <title>Authentication Error - VMS</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?= base_url().'assets/styles/scss/main.min.css' ?>" />
	<link rel="icon" href="<?php echo base_url('assets/images/icon-nr.png?v='.@filemtime(FCPATH.'assets/images/icon-nr.png')); ?>" />
    <style>
		.error-container {
			max-width: 600px;
			margin: 100px auto;
			padding: 40px;
			text-align: center;
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
		}
		
		.error-icon {
			font-size: 64px;
			color: #e74c3c;
			margin-bottom: 20px;
		}
		
		.error-title {
			font-size: 24px;
			color: #2c3e50;
			margin-bottom: 15px;
			font-weight: 600;
		}
		
		.error-message {
			font-size: 16px;
			color: #7f8c8d;
			margin-bottom: 30px;
			line-height: 1.6;
		}
		
		.error-actions {
			margin-top: 30px;
		}
		
		.btn {
			display: inline-block;
			padding: 12px 24px;
			background: #3498db;
			color: white;
			text-decoration: none;
			border-radius: 4px;
			margin: 0 10px;
			transition: background 0.3s;
		}
		
		.btn:hover {
			background: #2980b9;
			color: white;
			text-decoration: none;
		}
		
		.btn-secondary {
			background: #95a5a6;
		}
		
		.btn-secondary:hover {
			background: #7f8c8d;
		}
		
		.logo-container {
			margin-bottom: 30px;
		}
		
		.logo-container img {
			max-height: 60px;
		}
    </style>
</head>

<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center;">
	<div class="error-container">
		<div class="logo-container">
			<img src="<?= base_url().'assets/images/logo-nr.png?v='.@filemtime(FCPATH.'assets/images/logo-nr.png') ?>" alt="Logo">
		</div>
		
		<div class="error-icon">
			🔒
		</div>
		
		<h1 class="error-title">Authentication Failed</h1>
		
		<div class="error-message">
			<?php 
			if (isset($error_message) && !empty($error_message)) {
				echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
			} else {
				echo 'An authentication error occurred. Please try logging in again.';
			}
			?>
		</div>
		
		<div class="error-actions">
			<a href="<?php echo site_url(); ?>" class="btn">
				🏠 Return to Login
			</a>
			<a href="<?php echo site_url('main/logout'); ?>" class="btn btn-secondary">
				🚪 Clear Session
			</a>
		</div>
		
		<div style="margin-top: 40px; font-size: 12px; color: #bdc3c7;">
			<p>If you continue to experience issues, please contact your system administrator.</p>
			<p>Error Code: AUTH_EKS_FAILED | Time: <?php echo date('Y-m-d H:i:s'); ?></p>
		</div>
	</div>
</body>
</html> 
