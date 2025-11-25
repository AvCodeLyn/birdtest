<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : null;

// Lista dozwolonych podstron
$allowed = ['home', 'quiz', 'result', 'allresults', 'login', 'logout', 'questions'];

include 'includes/header.php';

if ($page && in_array($page, $allowed)) {
    include "pages/$page.php";
} else {
    include 'pages/home.php';
}

include 'includes/footer.php';
?>
