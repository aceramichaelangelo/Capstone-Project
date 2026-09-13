<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

tg_session_start();
if (!empty($_SESSION['tg_user']) && ($_SESSION['tg_user']['role'] ?? '') === 'admin') {
    header('Location: dashboard.php', true, 302);
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        $res = tg_attempt_admin_session($email, $password);
        if (!empty($res['ok'])) {
            header('Location: dashboard.php', true, 302);
            exit;
        }
        $error = (string)($res['message'] ?? 'Login failed.');
    }
}
$y = (int)date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login · <?= h(APP_BRAND) ?></title>
    <?php $assetV = (string)@filemtime(__DIR__ . '/assets/css/app.css'); ?>
    <link rel="stylesheet" href="assets/css/app.css?v=<?= h($assetV) ?>">
</head>
<body class="login-page login-page--split">
<div class="login-split-card">
    <div class="login-brand-panel">
        <div class="login-brand-inner">
            <div class="tg-logo" aria-hidden="true">
                <svg width="90" height="90" viewBox="0 0 90 90" fill="none" role="img" aria-label="Leaf logo">
                    <circle cx="45" cy="45" r="45" fill="#329b55"/>
                    <path d="M38.4 64.2C26.8 54.6 26.2 39.8 35.6 29.4c6.2-6.8 17.2-9.6 28.6-4.2 1.8.8 2.2 3.4.6 5.2C56.6 43.4 47.8 54.8 38.4 64.2Z" fill="#fff"/>
                    <path d="M40.2 61.2C46.4 48.6 53.2 36.4 60.8 26.8" stroke="#329b55" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="M43.8 54.2C40.2 51.4 37.4 48.2 35.6 44.6" stroke="#329b55" stroke-width="1.15" stroke-linecap="round"/>
                    <path d="M47.6 46.4C44.2 43.6 41.6 40.4 40.2 36.8" stroke="#329b55" stroke-width="1.15" stroke-linecap="round"/>
                    <path d="M51.8 38.2C48.8 35.8 46.6 33.2 45.4 30.2" stroke="#329b55" stroke-width="1.05" stroke-linecap="round"/>
                    <path d="M44.6 52.6C48.2 50.8 51.8 49.6 55.2 49.2" stroke="#329b55" stroke-width="1.15" stroke-linecap="round"/>
                    <path d="M48.4 44.6C51.8 43 55 42.2 58 42" stroke="#329b55" stroke-width="1.15" stroke-linecap="round"/>
                    <path d="M52.6 36.4C55.2 35.2 57.6 34.6 59.8 34.4" stroke="#329b55" stroke-width="1.05" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="login-brand-title"><?= h(APP_BRAND) ?></h1>
            <p class="login-brand-tagline">Admin Dashboard</p>
            <p class="login-brand-desc"><?= h(APP_NAME) ?></p>
            <ul class="login-brand-features">
                <li>Real-time dashboard &amp; flora metrics</li>
                <li>Scan records &amp; conservation status</li>
                <li>User &amp; role management</li>
                <li>Reports &amp; Mapbox map monitoring</li>
            </ul>
        </div>
    </div>

    <div class="login-form-panel">
        <div class="login-form-inner">
            <h2 class="login-form-heading">Admin Login</h2>
            <p class="login-form-sub">Enter your credentials to access the dashboard.</p>

            <?php if ($error !== '') : ?>
                <div class="alert alert-error"><?= h($error) ?></div>
            <?php endif; ?>
            

            <form method="post" action="login.php" autocomplete="on">
                <div class="field">
                    <label for="email">Email Address</label>
                    <div class="input-wrap input-wrap--light">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input id="email" name="email" type="email" placeholder="admin@davao.gov.ph" value="<?= h($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap input-wrap--light">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="password" name="password" type="password" placeholder="Password" required>
                        <button type="button" class="login-pw-toggle" id="pwToggle" aria-label="Show password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div class="row-between">
                    <label class="login-remember"><input type="checkbox" name="remember" value="1"> Remember me</label>
                    <a class="login-forgot" href="#">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-login-wide">
                    Sign In <span class="btn-login-arrow" aria-hidden="true">→</span>
                </button>
            </form>

            <div class="divider-or">OR</div>
            <p class="login-field-officer">Are you a field officer?</p>
            <a class="btn btn-outline btn-outline--block" href="mobile.php">Go to Mobile Interface</a>

            <p class="login-copyright">© <?= $y ?> <?= h(APP_BRAND) ?></p>
        </div>
    </div>
</div>
<script>
(function () {
  var p = document.getElementById('password');
  var t = document.getElementById('pwToggle');
  if (!p || !t) return;
  t.addEventListener('click', function () {
    p.type = p.type === 'password' ? 'text' : 'password';
  });
})();
</script>
</body>
</html>