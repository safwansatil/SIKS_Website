<?php
require_once 'auth.php';

$message = '';
$error = '';

// Check if there are pending password changes in session
if (!isset($_SESSION['change_pwd_pending_hash']) || !isset($_SESSION['change_pwd_otp'])) {
    header('Location: change_password.php');
    exit;
}

$mail_error = isset($_GET['mail_error']) && $_GET['mail_error'] == '1';
$dev_otp = isset($_GET['dev_otp']) ? $_GET['dev_otp'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp'] ?? '');

    if (empty($entered_otp)) {
        $error = 'Please enter the verification code.';
    } elseif (time() > $_SESSION['change_pwd_otp_expiry']) {
        $error = 'The verification code (OTP) has expired. Please request a new password change.';
        // Clear session data
        unset($_SESSION['change_pwd_pending_hash']);
        unset($_SESSION['change_pwd_otp']);
        unset($_SESSION['change_pwd_otp_expiry']);
    } elseif ($entered_otp != $_SESSION['change_pwd_otp']) {
        $error = 'Incorrect verification code. Please try again.';
    } else {
        try {
            // Commit password change to DB
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute([$_SESSION['change_pwd_pending_hash'], $_SESSION['admin_id']]);
            
            $message = 'Password updated successfully. You will be redirected shortly.';
            
            // Clean up session data
            unset($_SESSION['change_pwd_pending_hash']);
            unset($_SESSION['change_pwd_otp']);
            unset($_SESSION['change_pwd_otp_expiry']);
            
            // Redirect after 3 seconds
            header("refresh:3;url=index.php");
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Password Change - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        .card { background: white; padding: 2rem; border-radius: 12px; max-width: 400px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #065f46; text-align: center; font-size: 1.5rem; margin-bottom: 1rem; }
        p { color: #555; font-size: 0.9rem; line-height: 1.4; text-align: center; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 1.2rem; text-align: center; letter-spacing: 0.25em; font-weight: bold; }
        button { width: 100%; padding: 0.75rem; background: #065f46; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 1rem; font-size: 1rem; }
        button:hover { background: #047857; }
        .msg { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; text-align: center; font-size: 0.9rem; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .warning-box { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; padding: 1rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; text-align: left; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="change_password.php">&larr; Change Password</a>
        <a href="index.php">Dashboard</a>
    </div>

    <div class="card">
        <h1>Enter Verification Code</h1>
        
        <?php if ($mail_error && !empty($dev_otp)): ?>
            <div class="warning-box">
                <strong>Development Mode Helper:</strong><br>
                Email dispatch to <code>siks@iut-dhaka.edu</code> failed because the mail server is unconfigured. 
                <br><br>
                For testing purposes, your OTP is: <strong style="font-size: 1.1rem; color: #059669;"><?php echo htmlspecialchars($dev_otp); ?></strong>
            </div>
        <?php endif; ?>

        <p>A 6-digit One-Time Password (OTP) has been sent to <strong>siks@iut-dhaka.edu</strong>. Enter the code below to authorize the password update.</p>

        <?php if ($message): ?>
            <div class="msg success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>One-Time Password (OTP)</label>
                <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required autocomplete="off" placeholder="000000">
            </div>
            <button type="submit">Verify & Update Password</button>
        </form>
    </div>
</body>
</html>
