<?php
require_once __DIR__ . '/functions.php';

$action = $_GET['action'] ?? '';
// read POST data (support form-encoded and JSON)
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
$data = $_POST;
if((empty($data) || $jsonInput) && is_array($jsonInput)){
  $data = $jsonInput;
}
$isJson = (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if($action === 'register'){
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    if(!$name || !$email || !$password){
      $_SESSION['flash'] = 'Completați toate câmpurile.';
      header('Location: register.php'); exit;
    }
    $users = read_json('users.json');
    foreach($users as $u) if(strtolower($u['email']) === strtolower($email)){ $_SESSION['flash']='Email deja înregistrat.'; header('Location: register.php'); exit; }
    $next = 1; foreach($users as $u) $next = max($next, $u['id']); $next++;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $users[] = ['id'=>$next,'name'=>$name,'email'=>$email,'passwordHash'=>$hash];
    write_json('users.json', $users);
    $_SESSION['user'] = ['id'=>$next,'name'=>$name,'email'=>$email];
    if($isJson){ header('Content-Type: application/json'); echo json_encode(['user'=>$_SESSION['user']]); exit; }
    header('Location: dashboard.php'); exit;
  }
  if($action === 'login'){
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $users = read_json('users.json');
    foreach($users as $u){ if(strtolower($u['email']) === strtolower($email) && isset($u['passwordHash']) && password_verify($password, $u['passwordHash'])){ $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email']]; header('Location: dashboard.php'); exit; } }
    if($isJson){ header('Content-Type: application/json', true, 401); echo json_encode(['error'=>'Date de autentificare invalide.']); exit; }
    $_SESSION['flash']='Date de autentificare invalide.'; header('Location: login.php'); exit;
  }
}

if($action === 'logout'){
  session_destroy(); header('Location: login.php'); exit;
}

// fallback
header('Location: index.php');
exit;
