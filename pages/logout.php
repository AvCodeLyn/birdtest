<?php
require_once __DIR__ . '/../session.php';
$_SESSION = [];
session_unset();
session_destroy();
header("Location: index.php?page=login");
exit;