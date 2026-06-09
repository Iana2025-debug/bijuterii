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
      header('Location: /practica/register.html'); exit;
    }
    $users = read_json('users.json');
    foreach($users as $u) if(strtolower($u['email']) === strtolower($email)){ $_SESSION['flash']='Email deja înregistrat.'; header('Location: /practica/register.html'); exit; }
    $next = 1; foreach($users as $u) $next = max($next, $u['id']); $next++;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $users[] = ['id'=>$next,'name'=>$name,'email'=>$email,'passwordHash'=>$hash];
      write_json('users.json', $users);
      // verify write succeeded
      $verify = read_json('users.json');
      $found = false; foreach($verify as $u) if(isset($u['id']) && $u['id']===$next) { $found=true; break; }
      if(!$found){
        $_SESSION['flash'] = 'Eroare internă: nu s‑a putut salva utilizatorul (verifică permisiuni).';
        // attempt to log the problem
        @file_put_contents(__DIR__ . '/../data/auth_errors.log', date(DATE_ATOM) . " - write users failed for email: $email\n", FILE_APPEND);
        header('Location: /practica/register.html'); exit;
      }
    $_SESSION['user'] = ['id'=>$next,'name'=>$name,'email'=>$email];
    if($isJson){ header('Content-Type: application/json'); echo json_encode(['user'=>$_SESSION['user']]); exit; }
    header('Location: /practica/dashboard.html'); exit;
  }
  if($action === 'login'){
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $users = read_json('users.json');
    foreach($users as $u){ if(strtolower($u['email']) === strtolower($email) && isset($u['passwordHash']) && password_verify($password, $u['passwordHash'])){ $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email']]; header('Location: /practica/dashboard.php'); exit; } }
    if($isJson){ header('Content-Type: application/json', true, 401); echo json_encode(['error'=>'Date de autentificare invalide.']); exit; }
    $_SESSION['flash']='Date de autentificare invalide.'; header('Location: /practica/login.html'); exit;
  }
}

if($action === 'logout'){
  session_destroy(); header('Location: /practica/login.html'); exit;
}

// fallback
header('Location: /practica/index.html');
exit;
