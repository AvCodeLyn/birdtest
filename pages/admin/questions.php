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
$selectedQuizId = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : ($quizzes[0]['id'] ?? null);
$questions = $selectedQuizId ? $repo->getQuestions($selectedQuizId) : [];

$flashMessage = $_SESSION['flash_admin'] ?? '';
unset($_SESSION['flash_admin']);
?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h4 mb-1">Pytania</h1>
      <p class="text-muted mb-0">Twórz i edytuj pytania przypisane do quizów.</p>
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
      <form method="get" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="admin/questions">
        <div class="col-md-6">
          <label class="form-label">Wybierz quiz</label>
          <select name="quiz_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($quizzes as $quiz): ?>
              <option value="<?= (int) $quiz['id'] ?>" <?= $selectedQuizId == $quiz['id'] ? 'selected' : '' ?>><?= htmlspecialchars($quiz['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
  </div>

  <?php if (!$selectedQuizId): ?>
    <div class="alert alert-info">Brak quizów. Dodaj quiz, aby dodać pytania.</div>
  <?php else: ?>
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Dodaj pytanie</h5>
        <form method="post" action="index.php?page=admin/actions" class="row g-3">
          <input type="hidden" name="action" value="create_question">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="redirect" value="index.php?page=admin/questions&quiz_id=<?= (int) $selectedQuizId ?>">
          <input type="hidden" name="quiz_id" value="<?= (int) $selectedQuizId ?>">
          <div class="col-md-8">
            <label class="form-label">Treść pytania</label>
            <textarea name="text" class="form-control" rows="2" required></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label">Typ</label>
            <select name="type" class="form-select">
              <option value="single_choice">Jednokrotny wybór</option>
              <option value="multiple_choice">Wielokrotny wybór</option>
              <option value="text">Odpowiedź tekstowa</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Kolejność</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($questions) + 1 ?>" min="0">
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Zapisz pytanie</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Lista pytań</h5>
        <?php if (empty($questions)): ?>
          <p class="text-muted mb-0">Brak pytań dla tego quizu.</p>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($questions as $question): ?>
              <div class="list-group-item">
                <form method="post" action="index.php?page=admin/actions" class="row g-3 align-items-end">
                  <input type="hidden" name="action" value="update_question">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                  <input type="hidden" name="redirect" value="index.php?page=admin/questions&quiz_id=<?= (int) $selectedQuizId ?>">
                  <input type="hidden" name="question_id" value="<?= (int) $question['id'] ?>">
                  <div class="col-md-7">
                    <label class="form-label">Treść pytania</label>
                    <textarea name="text" class="form-control" rows="2" required><?= htmlspecialchars($question['text']) ?></textarea>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Typ</label>
                    <select name="type" class="form-select">
                      <option value="single_choice" <?= $question['type'] === 'single_choice' ? 'selected' : '' ?>>Jednokrotny wybór</option>
                      <option value="multiple_choice" <?= $question['type'] === 'multiple_choice' ? 'selected' : '' ?>>Wielokrotny wybór</option>
                      <option value="text" <?= $question['type'] === 'text' ? 'selected' : '' ?>>Odpowiedź tekstowa</option>
                    </select>
                  </div>
                  <div class="col-md-1">
                    <label class="form-label">Kolejność</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= (int) $question['sort_order'] ?>" min="0">
                  </div>
                  <div class="col-md-2 d-flex gap-2 align-items-center">
                    <button type="submit" class="btn btn-outline-primary w-100">Zapisz</button>
                    <a class="btn btn-outline-secondary w-100" href="index.php?page=admin/answers&quiz_id=<?= (int) $selectedQuizId ?>&question_id=<?= (int) $question['id'] ?>">Odpowiedzi</a>
                  </div>
                </form>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
