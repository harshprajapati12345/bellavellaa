<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/users/'); exit; }
// TODO: db_exec('DELETE FROM users WHERE id = ?', [$id]);
session_start();
$_SESSION['flash'] = ['type'=>'success','message'=>'User deleted.'];
header('Location: /bellavella/users/');
exit;
