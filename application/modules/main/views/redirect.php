<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Redirecting...</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin: 10px 0;
            line-height: 1.5;
        }
        .loader {
            margin: 20px auto;
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo isset($name) ? htmlspecialchars($name) : 'User'; ?>!</h1>
        <div class="loader"></div>
        <p>You are being redirected to the dashboard...</p>
        <p>If you are not redirected automatically, <a href="<?php echo BASE_LINK . 'dashboard'; ?>">click here</a>.</p>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "<?php echo BASE_LINK . 'dashboard'; ?>";
        }, 3000);
    </script>
</body>
</html>