<?php session_start(); $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="CSS/style.css"></head><body>
<main style="max-width:420px;margin:40px auto;padding:16px;background:#fff;border-radius:8px">
  <h2>Autentificare</h2>
  <?php if($flash): ?><div style="color:#c0392b"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
  <form method="post" action="php/auth.php?action=login">
    <label>Email<input type="email" name="email" required></label>
    <label>Parolă<input type="password" name="password" required></label>
    <button type="submit">Login</button>
  </form>
  <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
</main>
</body></html>
