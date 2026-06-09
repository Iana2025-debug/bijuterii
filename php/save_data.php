<?php
require_once __DIR__ . '/functions.php';

// Simple endpoint for saving orders via POST (expects JSON body)
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
  http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit;
}

$raw = file_get_contents('php://input');
$j = json_decode($raw, true);
if(!$j || empty($j['items'])){ http_response_code(400); echo json_encode(['error'=>'Invalid payload']); exit; }

$orders = read_json('orders.json');
$next = 1; foreach($orders as $o) $next = max($next, $o['id']); $next++;
$order = [
  'id'=>$next,
  'items'=>$j['items'],
  'total'=>$j['total'] ?? 0,
  'payment'=>isset($j['payment'])?['cardHolder'=>$j['payment']['cardHolder'] ?? null,'last4'=>$j['payment']['last4'] ?? null,'expiry'=>$j['payment']['expiry'] ?? null]:null,
  'createdAt'=>date(DATE_ATOM)
];

// Attach user id if available in session (allows users to view their own orders)
$current = current_user();
if($current && isset($current['id'])){ $order['userId'] = $current['id']; }
$orders[] = $order;
write_json('orders.json', $orders);
header('Content-Type: application/json'); echo json_encode(['orderId'=>$next]);
