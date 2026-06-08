<?php require_once __DIR__ . '/php/functions.php'; ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Contact</title><link rel="stylesheet" href="CSS/style.css"></head><body>
<main style="max-width:720px;margin:24px auto;padding:12px">
  <h1>Contactează-ne</h1>
  <form method="post" action="">
    <label>Nume<input name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Mesaj<textarea name="message" required></textarea></label>
    <button type="submit">Trimite</button>
  </form>
</main>
</body></html>
