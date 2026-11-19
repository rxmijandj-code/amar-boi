<?php
// Copy .env.example values into Render Environment. Never commit real credentials.
function envv(string $key, ?string $default=null): ?string { $v=getenv($key); return ($v===false || $v==='') ? $default : $v; }
function db(): PDO {
  static $pdo=null; if ($pdo instanceof PDO) return $pdo;
  $host=envv('DB_HOST'); $name=envv('DB_NAME'); $user=envv('DB_USER'); $pass=envv('DB_PASSWORD');
  if (!$host || !$name || !$user) throw new RuntimeException('Database configuration is incomplete.');
  $port=envv('DB_PORT','3306'); $charset='utf8mb4';
  $dsn="mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
  $pdo=new PDO($dsn,$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
  return $pdo;
}
function start_session(): void {
  if (session_status()===PHP_SESSION_ACTIVE) return;
  $secure=(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off');
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Strict']);
  session_start();
}
function json_out($data,int $status=200): never { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function require_admin(): void { start_session(); if (empty($_SESSION['admin'])) json_out(['ok'=>false,'error'=>'অনুমতি নেই। আবার Admin Login করুন।'],401); }
function check_csrf(): void { start_session(); $token=$_SERVER['HTTP_X_CSRF_TOKEN']??''; if (!$token || !hash_equals($_SESSION['csrf']??'', $token)) json_out(['ok'=>false,'error'=>'নিরাপত্তা যাচাই ব্যর্থ হয়েছে। পেজ রিফ্রেশ করুন।'],403); }
