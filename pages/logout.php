<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = new Auth($conn);
$auth->logout();
header("Location: index.php?page=login");
exit;