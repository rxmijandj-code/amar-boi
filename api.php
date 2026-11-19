<?php
require __DIR__.'/config.php';
$method=$_SERVER['REQUEST_METHOD']; $action=$_GET['action']??'';
try {
 if ($action==='bootstrap' && $method==='GET') {
  $content=db()->query("SELECT id,title,category,arabic,translation,surah,audio_url,summary,created_at FROM content WHERE is_visible=1 ORDER BY sort_order,id DESC")->fetchAll();
  $slides=db()->query("SELECT id,eyebrow,title,subtitle,image_url,link_url,sort_order FROM slides WHERE is_visible=1 ORDER BY sort_order,id DESC")->fetchAll();
  $settings=[]; foreach(db()->query("SELECT setting_key,setting_value FROM settings") as $row) $settings[$row['setting_key']]=$row['setting_value'];
  json_out(['ok'=>true,'content'=>$content,'slides'=>$slides,'settings'=>$settings]);
 }
 if ($action==='admin-data' && $method==='GET') {
  require_admin(); $pdo=db();
  $content=$pdo->query('SELECT * FROM content ORDER BY sort_order,id DESC')->fetchAll();
  $slides=$pdo->query('SELECT * FROM slides ORDER BY sort_order,id DESC')->fetchAll();
  $settings=[]; foreach($pdo->query('SELECT setting_key,setting_value FROM settings') as $row) $settings[$row['setting_key']]=$row['setting_value'];
  json_out(['ok'=>true,'content'=>$content,'slides'=>$slides,'settings'=>$settings]);
 }
 if ($action==='login' && $method==='POST') {
  start_session(); $body=json_decode(file_get_contents('php://input'),true)??[]; $pass=(string)($body['password']??'');
  $hash=envv('ADMIN_PASSWORD_HASH'); $plain=envv('ADMIN_PASSWORD');
  $valid=$hash ? password_verify($pass,$hash) : ($plain!==null && hash_equals($plain,$pass));
  if (!$valid) json_out(['ok'=>false,'error'=>'পাসওয়ার্ড সঠিক নয়।'],401);
  session_regenerate_id(true); $_SESSION['admin']=true; $_SESSION['csrf']=bin2hex(random_bytes(32));
  json_out(['ok'=>true,'csrf'=>$_SESSION['csrf']]);
 }
 if ($action==='logout' && $method==='POST') { require_admin(); check_csrf(); $_SESSION=[]; session_destroy(); json_out(['ok'=>true]); }
 if ($action==='session' && $method==='GET') { start_session(); json_out(['ok'=>true,'admin'=>!empty($_SESSION['admin']),'csrf'=>$_SESSION['csrf']??null]); }
 if ($action==='save' && $method==='POST') {
  require_admin(); check_csrf(); $b=json_decode(file_get_contents('php://input'),true)??[]; $pdo=db();
  $type=$b['type']??''; $v=$b['value']??[]; if (!is_array($v)) json_out(['ok'=>false,'error'=>'Invalid data'],400);
  if ($type==='content') {
   $id=(int)($v['id']??0); $fields=[$v['title']??'', $v['category']??'quran',$v['arabic']??'',$v['translation']??'',$v['surah']??'',$v['audio_url']??'',$v['summary']??'',!empty($v['is_visible'])?1:0,(int)($v['sort_order']??0)];
   if ($id>0) { $q=$pdo->prepare('UPDATE content SET title=?,category=?,arabic=?,translation=?,surah=?,audio_url=?,summary=?,is_visible=?,sort_order=? WHERE id=?'); $q->execute([...$fields,$id]); }
   else { $q=$pdo->prepare('INSERT INTO content(title,category,arabic,translation,surah,audio_url,summary,is_visible,sort_order) VALUES(?,?,?,?,?,?,?,?,?)'); $q->execute($fields); $id=(int)$pdo->lastInsertId(); }
   json_out(['ok'=>true,'id'=>$id]);
  }
  if ($type==='slide') {
   $id=(int)($v['id']??0); $fields=[$v['eyebrow']??'', $v['title']??'', $v['subtitle']??'', $v['image_url']??'', $v['link_url']??'',!empty($v['is_visible'])?1:0,(int)($v['sort_order']??0)];
   if ($id>0) { $q=$pdo->prepare('UPDATE slides SET eyebrow=?,title=?,subtitle=?,image_url=?,link_url=?,is_visible=?,sort_order=? WHERE id=?'); $q->execute([...$fields,$id]); }
   else { $q=$pdo->prepare('INSERT INTO slides(eyebrow,title,subtitle,image_url,link_url,is_visible,sort_order) VALUES(?,?,?,?,?,?,?)'); $q->execute($fields); $id=(int)$pdo->lastInsertId(); }
   json_out(['ok'=>true,'id'=>$id]);
  }
  if ($type==='settings') {
   $q=$pdo->prepare('INSERT INTO settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
   foreach($v as $k=>$value) { if (!in_array($k,['site_name','tagline','logo_url','reciter_name','reciter_audio_url','about_text'],true)) continue; $q->execute([$k,(string)$value]); }
   json_out(['ok'=>true]);
  }
  json_out(['ok'=>false,'error'=>'Unknown save type'],400);
 }
 if ($action==='delete' && $method==='POST') { require_admin(); check_csrf(); $b=json_decode(file_get_contents('php://input'),true)??[]; $table=($b['type']??'')==='slide'?'slides':(($b['type']??'')==='content'?'content':null); if(!$table) json_out(['ok'=>false,'error'=>'Invalid type'],400); $q=db()->prepare("DELETE FROM {$table} WHERE id=?"); $q->execute([(int)($b['id']??0)]); json_out(['ok'=>true]); }
 json_out(['ok'=>false,'error'=>'Not found'],404);
} catch(Throwable $e) { error_log((string)$e); json_out(['ok'=>false,'error'=>'সার্ভার বা Database সমস্যা হয়েছে। Environment ও SQL Import পরীক্ষা করুন।'],500); }
