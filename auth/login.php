<?php
require_once __DIR__ . '/../includes/config.php';
if (isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

$message = '';
if (!empty($_GET['error'])) {
    $errors = [
        'missing' => 'Please fill in all fields.',
        'invalid' => 'Email or password is incorrect.',
        'server' => 'Something went wrong. Please try again.',
    ];
    $message = $errors[$_GET['error']] ?? 'Unable to log in.';
}
if (!empty($_GET['success'])) {
    if ($_GET['success'] === 'registered') {
        $message = 'Account created. You are now logged in.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - LakbayPH</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
<?php include_once __DIR__ . '/../includes/navbar.php'; ?>
<section class="auth-page">
  <div class="auth-card">
    <div class="auth-header">
      <p class="auth-tag">Welcome back</p>
      <h1>Log in to LakbayPH</h1>
      <p class="auth-sub">Access your travel planner, saved hotels, and booking tools.</p>
    </div>

    <?php if ($message): ?>
      <div class="auth-alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form class="auth-form" action="login-process.php" method="POST">
      <div class="auth-field">
        <label for="email">Email address</label>
        <input id="email" type="email" name="email" placeholder="you@example.com" required>
      </div>
      <div class="auth-field">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" placeholder="Enter password" required>
      </div>
      <button class="auth-submit" type="submit">Log In</button>
    </form>

    <p class="auth-footer">No account yet? <a href="signup.php">Create one here</a></p>
  </div>
</section>
</body>
</html>