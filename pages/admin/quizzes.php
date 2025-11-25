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
      <h1 class="h4 mb-1">Quizy</h1>
      <p class="text-muted mb-0">Dodawaj i edytuj quizy.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="index.php?page=admin/dashboard" class="btn btn-outline-secondary">Panel</a>
      <a href="index.php?page=logout" class="btn btn-outline-danger">Wyloguj</a>
    </div>
  </div>

  <?php if (!empty($flashMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($flashMessage) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zamknij"></button>
    </div>
  <?php endif; ?>

  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <h5 class="card-title">Dodaj quiz</h5>
      <form method="post" action="index.php?page=admin/actions" class="row g-3">
        <input type="hidden" name="action" value="create_quiz">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="redirect" value="index.php?page=admin/quizzes">
        <div class="col-md-6">
          <label class="form-label">Tytuł</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="col-md-12">
          <label class="form-label">Opis</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Opcjonalny opis quizu"></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Zapisz quiz</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title">Istniejące quizy</h5>
      <?php if (empty($quizzes)): ?>
        <p class="text-muted mb-0">Brak utworzonych quizów.</p>
      <?php else: ?>
        <div class="list-group list-group-flush">
          <?php foreach ($quizzes as $quiz): ?>
            <div class="list-group-item">
              <form method="post" action="index.php?page=admin/actions" class="row g-3 align-items-end">
                <input type="hidden" name="action" value="update_quiz">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="redirect" value="index.php?page=admin/quizzes">
                <input type="hidden" name="quiz_id" value="<?= (int) $quiz['id'] ?>">
                <div class="col-md-4">
                  <label class="form-label">Tytuł</label>
                  <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($quiz['title']) ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Opis</label>
                  <textarea name="description" class="form-control" rows="2" placeholder="Opis quizu (opcjonalnie)"><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-2 d-flex gap-2 align-items-center">
                  <button type="submit" class="btn btn-outline-primary w-100">Zapisz</button>
                  <a class="btn btn-outline-secondary w-100" href="index.php?page=admin/questions&quiz_id=<?= (int) $quiz['id'] ?>">Pytania</a>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
