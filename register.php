<?php session_start(); $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="CSS/style.css"></head><body>
<main style="max-width:420px;margin:40px auto;padding:16px;background:#fff;border-radius:8px">
  <h2>Înregistrare</h2>
  <?php if($flash): ?><div style="color:#c0392b"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
  <form method="post" action="php/auth.php?action=register">
    <label>Nume<input type="text" name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Parolă<input type="password" name="password" required></label>
    <button type="submit">Înregistrează-te</button>
  </form>
  <p>Ai deja cont? <a href="login.php">Autentificare</a></p>
</main>
</body></html>
