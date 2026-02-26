<?php

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/categories/'); exit; }
// TODO: db_exec('DELETE FROM categories WHERE id = ?', [$id]);
session_start();
$_SESSION['flash'] = ['type'=>'success','message'=>'Category deleted.'];
header('Location: /bella/categories/');
exit;
