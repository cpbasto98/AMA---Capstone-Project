<?php
session_start();

// Load database config (this file is in the same folder as config.php)
$configPath = __DIR__ . '/config.php';
if (!file_exists($configPath)) {
    exit('Missing config.php — ensure it exists inside the logins folder.');
}
$config = require $configPath;

// Connect using PDO
try {
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit('Database connection failed.');
}

// Handle POST registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // Basic validation
    if ($username === '' || $password === '' || $confirm === '') {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: register.php');
        exit;
    }

    if ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: register.php');
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters.';
        header('Location: register.php');
        exit;
    }

    // Check if username already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);

    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Username is already taken.';
        header('Location: register.php');
        exit;
    }

    // Insert new user
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare(
        'INSERT INTO users (username, password, role) VALUES (:u, :p, :r)'
    );
    $insert->execute([
        ':u' => $username,
        ':p' => $hashed,
        ':r' => 'user', // default role
    ]);

    $_SESSION['success'] = 'Account created successfully! Please log in.';
    header('Location: login.php');
    exit;
}

// show message if exists
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create an Account</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            padding-top: 80px;
            background: #f4f4f4;
            font-family: Arial, Helvetica, sans-serif;
        }
        .register-box {
            width: 350px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .register-box h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .register-box input {
            width: 100%;
            padding: 10px;
            margin: 6px 0 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #4b60ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background: #3a4ed1;
        }
        .error {
            background: #ffdddd;
            color: #a10000;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: .9rem;
        }
        .success {
            background: #ddffdd;
            color: #0a8f00;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: .9rem;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            font-size: .9rem;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm" required>

        <button type="submit" class="btn">Register</button>
    </form>

    <a class="login-link" href="login.php">Already have an account? Login</a>
</div>

</body>
</html>
