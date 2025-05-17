<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>E-Procurement System</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .bgSh {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .loginTitle {
            text-align: center;
            margin-bottom: 20px;
        }
        .loginTitle h1 {
            color: #333;
            font-size: 24px;
            margin: 0;
        }
        .loginBox {
            text-align: center;
        }
        .loginBox p {
            margin: 10px 0;
            color: #666;
        }
        .loginBox a {
            color: #007bff;
            text-decoration: none;
        }
        .loginBox a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $content; ?>
    </div>
</body>
</html>