<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/professionals/'); exit; }
// TODO: db_exec('DELETE FROM professionals WHERE id = ?', [$id]);
session_start();
$_SESSION['flash'] = ['type'=>'success','message'=>'Professional removed.'];
header('Location: /bellavella/professionals/');
exit;
