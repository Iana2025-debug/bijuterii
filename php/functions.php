<?php
session_start();

function data_path($file){
  // store JSON files under data/ for safety
  return __DIR__ . '/../data/' . $file;
}

function read_json($file){
  $p = data_path($file);
  if(!file_exists($p)) return [];
  $txt = file_get_contents($p);
  $j = json_decode($txt, true);
  return $j ?: [];
}

function write_json($file, $data){
  $p = data_path($file);
  file_put_contents($p, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

function require_login(){
  if(empty($_SESSION['user'])){
    header('Location: login.php'); exit;
  }
}

function current_user(){
  return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

?>
