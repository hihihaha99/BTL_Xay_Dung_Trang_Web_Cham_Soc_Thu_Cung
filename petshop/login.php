<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';

$page_title = 'Log in | Pet Care';

// ---- CSRF token (đơn giản) ----
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$email_in = '';

// Lấy next (chỉ cho phép đường dẫn nội bộ để tránh open redirect)
$next = '/dashboard.php';
if (!empty($_GET['next'])) {
    $candidate = parse_url($_GET['next'], PHP_URL_PATH);
    if ($candidate && str_starts_with($candidate, '/')) {
        $next = $candidate;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $error = 'Security check failed. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $email_in = $email;

        if ($email === '' || $password === '') {
            $error = 'Please fill in both email and password.';
        } else {
            $stmt = $mysqli->prepare('SELECT id, name, email, password_hash FROM users WHERE email=? LIMIT 1');
            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $res = $stmt->get_result();
                $user = $res ? $res->fetch_assoc() : null;
                $stmt->close();

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user'] = [
                        'id'    => $user['id'],
                        'name'  => $user['name'],
                        'email' => $user['email']
                    ];
                    header('Location: ' . $next);
                    exit;
                } else {
                    $error = 'Incorrect email or password.';
                }
            } else {
                $error = 'Server is busy. Please try again.';
            }
        }
    }
}

include __DIR__ . '/partials/header.php';
?>

<!-- Auth two-column layout -->
<div class="auth-page">
  <div class="auth-container">
    <!-- Left intro -->
    <aside class="auth-intro" role="complementary" aria-label="About PetCare">
      <div class="intro-content">
        <h2>Welcome to <span>PetCare</span></h2>
        <p>
          We bring trusted veterinary care to your home. Book house calls,
          get urgent advice, or chat with our vets online — your pet’s health,
          our priority. Caring, convenient, and professional 🐾
        </p>
      </div>
    </aside>

    <!-- Right form -->
    <section class="auth-form" role="form" aria-label="Login form">
      <h2>Login</h2>

      <?php if ($error): ?>
        <div class="notice error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" autocomplete="on" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">

        <div class="input">
          <label for="email">Email</label>
          <input
            id="email"
            type="email"
            name="email"
            placeholder="you@example.com"
            value="<?= htmlspecialchars($email_in) ?>"
            autocomplete="email"
            required>
        </div>

        <div class="input">
          <label for="password">Password</label>
          <input
            id="password"
            type="password"
            name="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required>
        </div>

        <div class="row">
          <button type="submit" class="btn-accent">Log in</button>
          <a class="link" href="register.php">Register</a>
        </div>

        <hr class="divider" aria-hidden="true">
        <p class="social-text">Login with</p>

        <!-- Social login -->
        <div class="social-login">
          <a class="social google" href="oauth_google.php" title="Sign in with Google" aria-label="Sign in with Google">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path fill="#EA4335" d="M12 11h11a11 11 0 1 1-3.2-7.8l-2.7 2.6A6.6 6.6 0 1 0 18.6 12c0-.7-.1-1.2-.2-1.7H12z"/>
            </svg>
            Google
          </a>

          <a class="social facebook" href="oauth_facebook.php" title="Sign in with Facebook" aria-label="Sign in with Facebook">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path fill="#1877F2" d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.6V12h2.8V9.8c0-2.7 1.6-4.2 4-4.2 1.2 0 2.5.2 2.5.2v2.8h-1.4c-1.4 0-1.8.9-1.8 1.8V12h3.1l-.5 2.9H13.7v7A10 10 0 0 0 22 12z"/>
            </svg>
            Facebook
          </a>

          <a class="social instagram" href="oauth_instagram.php" title="Sign in with Instagram" aria-label="Sign in with Instagram">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path fill="#E6683C" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.8A5.2 5.2 0 1 1 6.8 13 5.2 5.2 0 0 1 12 7.8zm0 2a3.2 3.2 0 1 0 3.2 3.2A3.2 3.2 0 0 0 12 9.8zM18 6.7a1.2 1.2 0 1 1-1.2 1.2A1.2 1.2 0 0 1 18 6.7z"/>
            </svg>
            Instagram
          </a>
        </div>
      </form>
    </section>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
