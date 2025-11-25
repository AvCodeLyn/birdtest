<?php
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php?page=login');
    exit;
}

$csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if (empty($csrfToken) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    $_SESSION['flash'] = 'Nieprawidłowy token CSRF.';
    header('Location: index.php?page=allresults');
    exit;
}

require_once __DIR__ . '/db.php';

$changePassword = filter_input(INPUT_POST, 'change_quiz_password', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
$clearAll = filter_input(INPUT_POST, 'clear_all', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

if ($changePassword) {
    $newPassword = (string) filter_input(INPUT_POST, 'new_password', FILTER_UNSAFE_RAW);
    $newPassword = trim($newPassword);

    if ($newPassword === '') {
        $_SESSION['flash'] = 'Hasło nie może być puste.';
        header('Location: index.php?page=allresults');
        exit;
    }

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'quiz_password_hash'");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $stmt->close();

    $_SESSION['flash'] = 'Hasło do testu zostało zmienione.';
} elseif ($clearAll) {
    $stmt = $conn->prepare("DELETE FROM quiz_results");
    $stmt->execute();
    $stmt->close();

    $_SESSION['flash'] = 'Wszystkie odpowiedzi zostały usunięte.';
} else {
    $_SESSION['flash'] = 'Niepoprawna akcja.';
}

$conn->close();
header('Location: index.php?page=allresults');
exit;
?>
