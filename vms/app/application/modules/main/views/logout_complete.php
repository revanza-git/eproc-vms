<!DOCTYPE html>
<html>
<head>
    <title>Logout Complete - VMS eProc</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <style>
        .logout-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logout-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .logout-title {
            color: #333;
            margin-bottom: 20px;
        }
        .logout-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-login {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }
        .footer-info {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body style="background: #f1f3f4; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<div class="logout-container">
    <div class="logout-icon">
        ✓
    </div>
    
    <h2 class="logout-title">Logout Berhasil</h2>
    
    <div class="logout-message">
        <?php echo isset($message) ? $message : 'Anda telah berhasil logout dari sistem VMS eProc.'; ?>
        <br><br>
        Sesi Anda telah dihapus dan Anda dapat login kembali dengan aman.
    </div>
    
    <a href="<?php echo site_url(); ?>" class="btn-login">
        🔐 Login Kembali
    </a>
    
    <div class="footer-info">
        Sistem VMS eProc - PT Nusantara Regas<br>
        Logout completed at <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</div>

<script>
// Prevent back button after logout
if (window.history && window.history.pushState) {
    window.history.pushState(null, null, window.location.href);
    window.addEventListener('popstate', function() {
        window.history.pushState(null, null, window.location.href);
    });
}

// Auto-clear any remaining session storage
try {
    sessionStorage.clear();
    localStorage.removeItem('admin_session');
    localStorage.removeItem('user_session');
} catch(e) {
    // Ignore storage errors
}
</script>

</body>
</html> 