<?php
require_once __DIR__ . '/functions.php';
require_login();

$user = current_user();
$orders = read_json('orders.json');

$my = array_filter($orders, function($o) use ($user){ return isset($o['userId']) && $o['userId'] === $user['id']; });

if(empty($my)){
  header('Content-Type: text/plain; charset=utf-8');
  echo "Nu ai comenzi.";
  exit;
}

$filename = 'orders_' . $user['id'] . '_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// BOM for Excel/Windows compatibility
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, ['order_id','createdAt','total','item_name','item_qty','item_price']);

foreach($my as $o){
  if(empty($o['items'])) continue;
  foreach($o['items'] as $it){
    $row = [ $o['id'], $o['createdAt'] ?? '', $o['total'] ?? 0, $it['name'] ?? '', $it['qty'] ?? 1, $it['price'] ?? '' ];
    fputcsv($out, $row);
  }
}

fclose($out);
exit;
