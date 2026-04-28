<?php
/*
    File: login.php
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Admin login page for T's Travel.
                 Checks submitted credentials server-side using password_verify().
                 On success, sets $_SESSION['admin_logged_in'] and redirects to admin.php.
                 On failure, displays an error message without revealing which field was wrong.
*/

session_start();

// If already logged in, skip the login page entirely
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit;
}

// =====================================================================
// CREDENTIALS
// The password is stored as a bcrypt hash — never as plain text.
// To regenerate: echo password_hash('your_password', PASSWORD_DEFAULT);
// =====================================================================
$admin_username      = 'isaac_admin';
$admin_password_hash = '$2y$12$Qm33bREhMMVdX7KwrcHnNe9p5xeocgbciOv2Vvc24Z.4oyM5VyVdu';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submitted_user = trim($_POST['username'] ?? '');
    $submitted_pass = $_POST['password']     ?? '';

    // Check username first, then verify hashed password.
    // password_verify() safely compares the plain-text input against the hash
    // without ever storing or exposing the real password.
    if ($submitted_user === $admin_username
        && password_verify($submitted_pass, $admin_password_hash)) {

        // Credentials correct — mark this session as authenticated
        $_SESSION['admin_logged_in'] = true;

        // Redirect to admin panel
        header('Location: admin.php');
        exit;

    } else {
        // Generic error — don't reveal whether username or password was wrong
        $login_error = 'Invalid username or password. Please try again.';
    }
}
$page_title  = "Admin Login - T's Travel";
$active_page = 'login';
require_once 'header.php';
?>

    <header class="hero">
        <div class="hero-content">
            <h1>🔒 Admin Login</h1>
            <p>Restricted area — authorised personnel only</p>
        </div>
    </header>

    <main>
        <section class="content-section">
            <div style="max-width: 420px; margin: 0 auto;">
                <article class="service-category">

                    <h2 style="font-family: 'Playfair Display', serif; color: var(--primary-blue); margin-bottom: 20px;">
                        Sign In
                    </h2>

                    <?php if ($login_error !== ''): ?>
                        <p class="form-message error">
                            <?= htmlspecialchars($login_error) ?>
                        </p>
                    <?php endif; ?>

                    <form action="login.php" method="POST" novalidate>

                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username"
                                autocomplete="username" placeholder="Admin username">
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password"
                                autocomplete="current-password" placeholder="Admin password">
                        </div>

                        <button type="submit" class="cta-button" style="width: 100%; margin-top: 10px;">
                            Log In
                        </button>

                    </form>

                </article>
            </div>
        </section>
    </main>


<?php $footer_minimal = true; require_once 'footer.php'; ?>
