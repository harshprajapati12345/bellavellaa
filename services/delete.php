<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bellavella/services/'); exit; }
// TODO: db_exec('DELETE FROM services WHERE id = ?', [$id]);
session_start();
$_SESSION['flash'] = ['type'=>'success','message'=>'Service deleted.'];
header('Location: /bellavella/services/');
exit;
