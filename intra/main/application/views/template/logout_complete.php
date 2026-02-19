<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Complete</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #007cba;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
        .btn:hover {
            background-color: #005a87;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="success-icon">✓</div>
        <h1>Logout Successful</h1>
        <p>You have been successfully logged out from all applications.</p>
        <a href="<?php echo rtrim(config_item('vms_url'), '/').'/main/logout?from_main=1'; ?>" class="btn">Return to Login</a>
    </div>
</body>
</html>
