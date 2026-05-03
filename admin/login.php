<?php
require_once 'auth.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        if (!$pdo) {
            $error = 'Database connection failed. Please check your credentials in includes/db.php.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SIKS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #065f46;
            --bg: #f8fafc;
            --secondary: #1e293b;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            background: radial-gradient(circle at top right, #ecfdf5, #f8fafc); 
            color: var(--secondary);
        }
        .login-box { 
            background: white; 
            padding: 3rem 2.5rem; 
            border-radius: 1.5rem; 
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
            width: 100%;
            max-width: 400px; 
            text-align: center;
        }
        .logo {
            width: 64px;
            height: 64px;
            background: var(--primary);
            color: white;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3);
        }
        h1 { 
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem; 
            margin-bottom: 0.5rem; 
            color: var(--secondary);
            font-weight: 700;
        }
        p.subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
        .error { 
            background: #fef2f2;
            color: #991b1b; 
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem; 
            font-size: 0.875rem; 
            font-weight: 500;
            border: 1px solid #fecaca;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.25rem;
        }
        label { 
            display: block; 
            font-weight: 600; 
            font-size: 0.875rem; 
            margin-bottom: 0.5rem;
            color: #475569;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        input { 
            width: 100%; 
            padding: 0.75rem 1rem 0.75rem 2.75rem; 
            border: 1px solid #e2e8f0; 
            border-radius: 0.75rem; 
            box-sizing: border-box; 
            font-family: inherit;
            transition: all 0.2s;
            background: #f8fafc;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }
        button { 
            width: 100%; 
            padding: 0.875rem; 
            background: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 0.75rem; 
            cursor: pointer; 
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.2s;
            margin-top: 1rem;
        }
        button:hover { 
            background: var(--primary-dark); 
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.3);
        }
        button:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1>SIKS Admin</h1>
        <p class="subtitle">Enter your credentials to access the portal</p>

        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" required placeholder="admin">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
            </div>
            <button type="submit">Sign In</button>
        </form>
        
        <p style="margin-top: 2rem; font-size: 0.75rem; color: #94a3b8;">
            &copy; <?php echo date('Y'); ?> IUT-SIKS. All rights reserved.
        </p>
    </div>
</body>
</html>
