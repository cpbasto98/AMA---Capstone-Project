<?php
session_start();

// CONFIG PATH: config.php is inside the same folder (logins/config.php)
$configPath = __DIR__ . '/config.php';
if (!file_exists($configPath)) {
    exit('Missing config.php — create it as instructed.');
}
$config = require $configPath; // config.php should return an array with db_host, db_name, db_user, db_pass

// Optional: set secure session cookie parameters in production (HTTPS)
// if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
//     session_set_cookie_params([
//         'lifetime' => 0,
//         'path' => '/',
//         'domain' => $_SERVER['HTTP_HOST'],
//         'secure' => true,
//         'httponly' => true,
//         'samesite' => 'Lax'
//     ]);
// }

try {
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Log $e->getMessage() to a file in production instead of echoing
    exit('Database connection failed. Make sure MySQL is running and config.php has correct credentials.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $_SESSION['error'] = 'Please enter username and password.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Use prepared statement (you already do — good)
    $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login success
        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        // Redirect to dashboard — adjust path to where you'll put it
        header('Location: ../dashboard/dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = 'Incorrect username or password.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// For GET: show and clear any session error
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
