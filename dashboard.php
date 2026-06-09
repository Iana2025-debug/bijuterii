<?php require_once __DIR__ . '/php/functions.php'; require_login(); $user = current_user(); $users = read_json('users.json'); $orders = read_json('orders.json'); ?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="CSS/style.css"></head><body>
<main style="max-width:900px;margin:24px auto;padding:12px">
  <h1>Dashboard</h1>
  <p>Bine ai venit, <?php echo htmlspecialchars($user['name']); ?>. (<a href="php/auth.php?action=logout">Logout</a>)</p>
  <section>
    <h2>Comenzi</h2>
    <?php if(empty($orders)): ?><div>Nu există comenzi.</div><?php else: ?>
      <ul><?php foreach($orders as $o): ?><li>#<?php echo $o['id']; ?> — <?php echo htmlspecialchars($o['total']); ?> — <?php echo htmlspecialchars($o['createdAt']); ?><?php if(isset($o['userId'])) echo ' — User: ' . htmlspecialchars($o['userId']); ?></li><?php endforeach; ?></ul>
    <?php endif; ?>
  </section>
  <section>
    <h2>Comenzile mele</h2>
    <p><a class="btn-outline" href="php/export_orders.php">Exportă CSV</a></p>
    <?php
      $my = [];
      foreach($orders as $o) if(isset($o['userId']) && $o['userId'] === $user['id']) $my[] = $o;
    ?>
    <?php if(empty($my)): ?><div>Nu ai plasat încă nicio comandă.</div><?php else: ?>
      <ul>
      <?php foreach($my as $o): ?>
        <li>
          <strong>#<?php echo $o['id']; ?></strong> — <?php echo htmlspecialchars($o['total']); ?> — <?php echo htmlspecialchars($o['createdAt']); ?>
          <ul>
            <?php foreach($o['items'] as $it): ?>
              <li><?php echo htmlspecialchars(($it['name'] ?? 'Produs') . ' ×' . ($it['qty'] ?? 1) . ' — ' . ($it['price'] ?? 0) . ' MDL'); ?></li>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
  <section>
    <h2>Utilizatori</h2>
    <?php if(empty($users)): ?><div>Niciun utilizator.</div><?php else: ?><ul><?php foreach($users as $u): ?><li><?php echo htmlspecialchars($u['id'] . ' — ' . $u['name'] . ' <' . $u['email'] . '>'); ?></li><?php endforeach; ?></ul><?php endif; ?>
  </section>
</main>
</body></html>
