<?php require_once __DIR__ . '/php/functions.php'; require_login(); $user = current_user(); $users = read_json('users.json'); $orders = read_json('orders.json'); ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="CSS/style.css"></head><body>
<main style="max-width:900px;margin:24px auto;padding:12px">
  <h1>Dashboard</h1>
  <p>Bine ai venit, <?php echo htmlspecialchars($user['name']); ?>. (<a href="php/auth.php?action=logout">Logout</a>)</p>
  <section>
    <h2>Comenzi</h2>
    <?php if(empty($orders)): ?><div>Nu există comenzi.</div><?php else: ?>
      <ul><?php foreach($orders as $o): ?><li>#<?php echo $o['id']; ?> — <?php echo htmlspecialchars($o['total']); ?> — <?php echo htmlspecialchars($o['createdAt']); ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
  </section>
  <section>
    <h2>Utilizatori</h2>
    <?php if(empty($users)): ?><div>Niciun utilizator.</div><?php else: ?><ul><?php foreach($users as $u): ?><li><?php echo htmlspecialchars($u['id'] . ' — ' . $u['name'] . ' <' . $u['email'] . '>'); ?></li><?php endforeach; ?></ul><?php endif; ?>
  </section>
</main>
</body></html>
