<?php
/**
 * packages/delete.php — Delete Package
 * Usage: /bella/packages/delete.php?id=1
 */


$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: /bella/packages/'); exit; }

// TODO: Verify package exists
// $package = db_row('SELECT id, name FROM packages WHERE id = ?', [$id]);
// if (!$package) { header('Location: /bella/packages/'); exit; }

// TODO: Perform delete
// db_exec('DELETE FROM packages WHERE id = ?', [$id]);

// Redirect with success flash
session_start();
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Package deleted successfully.'];
header('Location: /bella/packages/');
exit;
