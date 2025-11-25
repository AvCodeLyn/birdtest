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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$repo = new QuizRepository($conn);
$quizzes = $repo->getQuizzes();

$flashMessage = $_SESSION['flash_admin'] ?? '';
unset($_SESSION['flash_admin']);
?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-1">Panel administracyjny</h1>
      <p class="text-muted mb-0">Zarządzaj quizami, pytaniami i odpowiedziami.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="index.php?page=allresults" class="btn btn-outline-secondary">Wyniki</a>
      <a href="index.php?page=logout" class="btn btn-outline-danger">Wyloguj</a>
    </div>
  </div>

  <?php if (!empty($flashMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flashMessage) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zamknij"></button>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Quizy</h5>
          <p class="card-text text-muted">Dodaj nowe quizy lub edytuj istniejące.</p>
          <a href="index.php?page=admin/quizzes" class="btn btn-primary">Przejdź do quizów</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Pytania</h5>
          <p class="card-text text-muted">Zarządzaj treścią, typem i kolejnością pytań.</p>
          <a href="index.php?page=admin/questions" class="btn btn-primary">Przejdź do pytań</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Odpowiedzi</h5>
          <p class="card-text text-muted">Edytuj treść oraz punktację odpowiedzi.</p>
          <a href="index.php?page=admin/answers" class="btn btn-primary">Przejdź do odpowiedzi</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card mt-4 shadow-sm">
    <div class="card-body">
      <h5 class="card-title">Przegląd quizów</h5>
      <?php if (empty($quizzes)): ?>
        <p class="text-muted mb-0">Brak quizów. Dodaj pierwszy, korzystając z zakładki Quizy.</p>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($quizzes as $quiz): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong><?= htmlspecialchars($quiz['title']) ?></strong><br>
                <small class="text-muted">ID: <?= (int) $quiz['id'] ?></small>
              </div>
              <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-primary" href="index.php?page=admin/questions&quiz_id=<?= (int) $quiz['id'] ?>">Pytania</a>
                <a class="btn btn-sm btn-outline-secondary" href="index.php?page=admin/answers&quiz_id=<?= (int) $quiz['id'] ?>">Odpowiedzi</a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
