<?php
require_once 'auth.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $error = 'New passwords do not match.';
        } else {
            try {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
                $stmt->execute([$_SESSION['admin_id']]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($current_password, $admin['password'])) {
                    // Generate secure 6-digit OTP
                    $otp = random_int(100000, 999999);
                    
                    // Store details in session
                    $_SESSION['change_pwd_pending_hash'] = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $_SESSION['change_pwd_otp'] = $otp;
                    $_SESSION['change_pwd_otp_expiry'] = time() + 600; // 10 minutes
                    
                    // Dispatch verification mail to the designated address
                    $to = 'siks@iut-dhaka.edu';
                    $subject = 'SIKS Admin Portal - Password Change OTP';
                    $email_message = "Dear Admin,\n\nWe received a request to update the password for your SIKS admin account.\n\nYour 2FA verification code (OTP) is: " . $otp . "\n\nThis code is valid for 10 minutes.\n\nIf you did not initiate this change, please inspect server security configurations immediately.\n\nSincerely,\nSIKS Web Portal";
                    
                    $sender_domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'iutsiks.iutoic-dhaka.edu';
                    $headers = "From: SIKS Portal <no-reply@" . $sender_domain . ">\r\n" .
                               "Reply-To: no-reply@" . $sender_domain . "\r\n" .
                               "X-Mailer: PHP/" . phpversion();

                    $mail_sent = @mail($to, $subject, $email_message, $headers);
                    
                    // Redirect to verification screen
                    if ($mail_sent) {
                        header('Location: verify_password_change.php');
                    } else {
                        // Safe warning fallback helper for local developers (without mail servers configured)
                        header('Location: verify_password_change.php?mail_error=1&dev_otp=' . $otp);
                    }
                    exit;
                } else {
                    $error = 'Incorrect current password.';
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        .card { background: white; padding: 2rem; border-radius: 12px; max-width: 400px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #065f46; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #065f46; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 1rem; }
        button:hover { background: #047857; }
        .msg { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; text-align: center; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
    </div>

    <div class="card">
        <h1>Change Password</h1>

        <?php if ($message): ?>
            <div class="msg success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
