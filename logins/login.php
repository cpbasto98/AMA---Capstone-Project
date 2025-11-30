<?php
session_start();

// CONFIG PATH: adjust if your config.php is in a different folder.
// I assumed config.php sits one level above this file (../config.php).
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    exit('Missing config.php — create it as instructed.');
}
$config = require $configPath;

// Connect to DB using PDO
try {
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production do not echo detailed errors.
    exit('Database connection failed. Make sure MySQL is running and config.php has correct credentials.');
}

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $_SESSION['error'] = 'Please enter username and password.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login success
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../dashboard.php'); // adjust path if your dashboard is elsewhere
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Official website of Barangay Rizal, Taguig City. Connecting our community through transparency, service, and innovation.">
  <title>Barangay Rizal Official Website</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body>

<header>
  <div class="navbar-container">
    <div class="navbar">
      <div class="logo-section">
        <img src="../images/barangay-logo.png" alt="Barangay Rizal Logo" class="logo">
        <img src="../images/cityoftaguig-logo.png" alt="City of Taguig Logo" class="logo">
        <h1>Barangay Rizal, Taguig City</h1>
      </div>

      <nav>
        <a href="../index.html">Home</a>
        <a href="../announcement.html">Announcements</a>
        <a href="../Services/appointment-scheduling.html">Online Appointment</a>

        <!-- SERVICES DROPDOWN -->
        <div class="dropdown">
          <span class="dropbtn">Services</span>
          <div class="dropdown-content">
            <a href="../Services/certificate-request.html">Certificate Request</a>
            <a href="../Services/file-a-complaint.html">File a Complaint</a>
            <a href="../Services/downloadable-forms.html">Downloadable Forms</a>
          </div>
        </div>

        <!-- PEOPLE DROPDOWN -->
        <div class="dropdown">
          <span class="dropbtn">People</span>
          <div class="dropdown-content">
            <a href="../People/barangay-council.html">Barangay Council</a>
            <a href="../People/sanguniang-kabataan.html">Sangguniang Kabataan (SK)</a>
            <a href="../People/support-team.html">Support Team</a>
            <a href="../People/it-team.html">IT Team</a>
          </div>
        </div>

        <a href="../contact.html">Contact</a>
        <a href="../about.html">About Us</a>
        <a href="../logins/login.php" class="active">Employee Login</a>

      </nav>

    </div>
  </div>
</header>

<main>

  <!-- LOGIN SECTION -->
  <section class="login-section" style="display:flex; justify-content:center; padding:40px 20px;">
    <div class="login-card" style="width:360px; background:#fff; padding:28px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08);">
      <div style="text-align:center; margin-bottom:18px;">
        <img src="../images/barangay-logo.png" alt="Logo" style="height:64px;">
        <h2 style="margin:10px 0 0 0;">Barangay Rizal Login</h2>
        <p style="color:#555; margin:6px 0 0 0;">Sign in to access resident services</p>
      </div>

      <!-- Error message -->
      <?php if (!empty($error)): ?>
        <p style="color:red; text-align:center; margin-bottom:12px;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <!-- The form posts to the same file -->
      <form action="" method="POST">
        <label style="display:block; font-weight:600; margin-top:8px;">Username</label>
        <input type="text" name="username" required style="width:100%; padding:10px; margin-top:6px; border-radius:6px; border:1px solid #ccc;">

        <label style="display:block; font-weight:600; margin-top:12px;">Password</label>
        <input type="password" name="password" required style="width:100%; padding:10px; margin-top:6px; border-radius:6px; border:1px solid #ccc;">

        <button type="submit" name="login" style="width:100%; margin-top:16px; padding:12px; border:none; border-radius:6px; background:#1b5cc6; color:#fff; font-weight:700; cursor:pointer;">
          Login
        </button>
      </form>

      <p style="text-align:center; margin-top:12px;">
        <a href="register.php">Register</a> • <a href="forgot-password.php">Forgot password?</a>
      </p>
    </div>
  </section>

</main>


<footer class="footer">
  <div class="footer-container">
    <!-- ABOUT BARANGAY SECTION -->
    <div class="footer-column">
      <div class="footer-logos">
        <img src="../images/barangay-logo.png" alt="Barangay Rizal Logo">
        <img src="../images/cityoftaguig-logo.png" alt="Taguig City Logo">
        <img src="../images/phseal.png" alt="Philippine Seal">
      </div>
      <p>
        Barangay Rizal is a vibrant community that upholds transparency,
        unity, and efficient public service for all its residents. 
      </p>
    </div>

    <!-- QUICK LINKS -->
    <div class="footer-column">
      <h3>QUICK LINKS</h3>
      <ul>
        <li><a href="about.html">About Our Barangay</a></li>
        <li><a href="officials.html">Barangay Officials</a></li>
        <li><a href="departments.html">Departments</a></li>
        <li><a href="gad.html">Gender and Development</a></li>
        <li><a href="contact.html">Contact Us</a></li>
        <li><a href="privacy.html">Privacy Policy</a></li>
      </ul>
    </div>

    <!-- CONTACT INFO -->
    <div class="footer-column">
      <h3>BARANGAY RIZAL TAGUIG</h3>
      <p><strong>📍</strong> Blk 99 Lot 19 J.P. Rizal St., Barangay Rizal, Taguig City </p>
      <p><strong>📞</strong> (02)728-7970, 09398013838, 09052706030 </p>
      <p><strong>✉️</strong> official.barangayrizal@gmail.com </p>
      <h4>FOLLOW US</h4>
      <div class="social-links">
        <a href="https://www.facebook.com/p/Barangay-Rizal-Taguig-City-100057153322641/"><img src="../images/facebook-icon.png" alt="Facebook"></a>
      </div>
    </div>

    <!-- MAP SECTION -->
    <div class="footer-column map">
      <h3>MAP AND LOCATION</h3>
      <iframe
        src="https://www.google.com/maps?q=Barangay%20Rizal%20Taguig&output=embed"
        width="180%"
        height="180"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </div>

 <!-- GOVPH FOOTER SECTION -->
<section class="govph-footer">
  <div class="govph-container">
    <!-- Republic of the Philippines -->
    <div class="govph-column">
      <img src="../images/phseal.png" alt="Republic of the Philippines Logo" class="govph-logo">
      <h3>REPUBLIC OF THE PHILIPPINES</h3>
      <p>
        All content is in the public domain unless otherwise stated.
      </p>
    </div>

    <!-- Freedom of Information -->
    <div class="govph-column">
      <img src="../images/foi-logo.png" alt="Freedom of Information Logo" class="govph-logo">
      <h3>FREEDOM OF INFORMATION</h3>
      <p>
        Learn more about the Executive Order No. 2 – The order implementing Freedom of Information in the Philippines.
      </p>
    </div>

    <!-- About GOVPH -->
    <div class="govph-column">
      <h3>ABOUT GOVPH</h3>
      <p>
        Learn more about the Philippine government, its structure, how government works, and the people behind it.
      </p>
      <ul>
        <li><a href="https://www.gov.ph/" target="_blank">GOV.PH</a></li>
        <li><a href="https://data.gov.ph/" target="_blank">Open Data Portal</a></li>
        <li><a href="https://www.officialgazette.gov.ph/" target="_blank">Official Gazette</a></li>
      </ul>
    </div>

    <!-- Executive -->
    <div class="govph-column">
      <h3>EXECUTIVE</h3>
      <ul>
        <li><a href="https://op-proper.gov.ph/" target="_blank">Office of the President</a></li>
        <li><a href="https://sc.judiciary.gov.ph/" target="_blank">Sandiganbayan</a></li>
        <li><a href="https://senate.gov.ph/" target="_blank">Senate of the Philippines</a></li>
        <li><a href="https://congress.gov.ph/" target="_blank">House of Representatives</a></li>
        <li><a href="https://doh.gov.ph/" target="_blank">Department of Health</a></li>
        <li><a href="https://dof.gov.ph/" target="_blank">Department of Finance</a></li>
      </ul>

      <h3>LEGISLATIVE</h3>
      <ul>
        <li><a href="https://senate.gov.ph/" target="_blank">Senate of the Philippines</a></li>
        <li><a href="https://congress.gov.ph/" target="_blank">House of Representatives</a></li>
      </ul>
    </div>

    <!-- Judiciary -->
    <div class="govph-column">
      <h3>JUDICIARY</h3>
      <ul>
        <li><a href="https://sc.judiciary.gov.ph/" target="_blank">Supreme Court</a></li>
        <li><a href="https://ca.judiciary.gov.ph/" target="_blank">Court of Appeals</a></li>
        <li><a href="https://sb.judiciary.gov.ph/" target="_blank">Sandiganbayan</a></li>
        <li><a href="https://cta.judiciary.gov.ph/" target="_blank">Court of Tax Appeals</a></li>
        <li><a href="https://judiciary.gov.ph/" target="_blank">Judicial Bar and Council</a></li>
      </ul>
    </div>
  </div>

  <div class="govph-divider"></div>

  <p class="govph-bottom">
    © 2025 Barangay Rizal, Taguig City | Developed by Ian, Kurt and Ranel
  </p>
</section>

</footer>

  <!-- 🧠 Chatbase Chatbot Embed -->
  <script>
    (function() {
      if (!window.chatbase || window.chatbase("getState") !== "initialized") {
        window.chatbase = (...arguments) => {
          if (!window.chatbase.q) {
            window.chatbase.q = [];
          }
          window.chatbase.q.push(arguments);
        };
        window.chatbase = new Proxy(window.chatbase, {
          get(target, prop) {
            if (prop === "q") {
              return target.q;
            }
            return (...args) => target(prop, ...args);
          }
        });
      }
      const onLoad = function() {
        const script = document.createElement("script");
        script.src = "https://www.chatbase.co/embed.min.js";
        script.id = "IaYd6HQ9H5meTLVNz7fNu";
        script.domain = "www.chatbase.co";
        document.body.appendChild(script);
      };
      if (document.readyState === "complete") {
        onLoad();
      } else {
        window.addEventListener("load", onLoad);
      }
    })();
  </script>
</body>
</html>