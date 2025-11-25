<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../includes/QuizRepository.php';

function verifyCsrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function redirectWithMessage(string $redirect, string $message): void
{
    $_SESSION['flash_admin'] = $message;
    header('Location: ' . $redirect);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?page=admin/dashboard');
    exit;
}

$action = $_POST['action'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';
$redirect = $_POST['redirect'] ?? 'index.php?page=admin/dashboard';

if (!verifyCsrf($csrfToken)) {
    redirectWithMessage($redirect, 'Nieprawidłowy token bezpieczeństwa.');
}

$repo = new QuizRepository($conn);

switch ($action) {
    case 'create_quiz':
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($title === '') {
            redirectWithMessage($redirect, 'Tytuł quizu jest wymagany.');
        }
        $repo->createQuiz($title, $description);
        redirectWithMessage($redirect, 'Quiz został zapisany.');
        break;

    case 'update_quiz':
        $quizId = (int)($_POST['quiz_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($quizId <= 0 || $title === '') {
            redirectWithMessage($redirect, 'Błędne dane quizu.');
        }
        $repo->updateQuiz($quizId, $title, $description);
        redirectWithMessage($redirect, 'Quiz został zaktualizowany.');
        break;

    case 'create_question':
        $quizId = (int)($_POST['quiz_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');
        $type = $_POST['type'] ?? 'single_choice';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $allowedTypes = ['single_choice', 'multiple_choice', 'text'];
        if ($quizId <= 0 || $text === '' || !in_array($type, $allowedTypes, true)) {
            redirectWithMessage($redirect, 'Błędne dane pytania.');
        }
        $repo->createQuestion($quizId, $text, $type, $sortOrder);
        redirectWithMessage($redirect, 'Pytanie zostało zapisane.');
        break;

    case 'update_question':
        $questionId = (int)($_POST['question_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');
        $type = $_POST['type'] ?? 'single_choice';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $allowedTypes = ['single_choice', 'multiple_choice', 'text'];
        if ($questionId <= 0 || $text === '' || !in_array($type, $allowedTypes, true)) {
            redirectWithMessage($redirect, 'Błędne dane pytania.');
        }
        $repo->updateQuestion($questionId, $text, $type, $sortOrder);
        redirectWithMessage($redirect, 'Pytanie zostało zaktualizowane.');
        break;

    case 'create_answer':
        $questionId = (int)($_POST['question_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $weight = (int)($_POST['weight'] ?? 0);
        if ($questionId <= 0 || $content === '') {
            redirectWithMessage($redirect, 'Błędne dane odpowiedzi.');
        }
        $repo->createAnswer($questionId, $content, $weight);
        redirectWithMessage($redirect, 'Odpowiedź została zapisana.');
        break;

    case 'update_answer':
        $answerId = (int)($_POST['answer_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $weight = (int)($_POST['weight'] ?? 0);
        if ($answerId <= 0 || $content === '') {
            redirectWithMessage($redirect, 'Błędne dane odpowiedzi.');
        }
        $repo->updateAnswer($answerId, $content, $weight);
        redirectWithMessage($redirect, 'Odpowiedź została zaktualizowana.');
        break;

    default:
        redirectWithMessage($redirect, 'Nieobsługiwana akcja.');
        break;
}
