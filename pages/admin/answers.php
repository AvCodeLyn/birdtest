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
$selectedQuestionId = isset($_GET['question_id']) ? (int) $_GET['question_id'] : ($questions[0]['id'] ?? null);
$answers = $selectedQuestionId ? $repo->getAnswers($selectedQuestionId) : [];

$flashMessage = $_SESSION['flash_admin'] ?? '';
unset($_SESSION['flash_admin']);
?>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h4 mb-1">Odpowiedzi</h1>
      <p class="text-muted mb-0">Dodawaj i edytuj odpowiedzi oraz punktację.</p>
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
        <input type="hidden" name="page" value="admin/answers">
        <div class="col-md-4">
          <label class="form-label">Quiz</label>
          <select name="quiz_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($quizzes as $quiz): ?>
              <option value="<?= (int) $quiz['id'] ?>" <?= $selectedQuizId == $quiz['id'] ? 'selected' : '' ?>><?= htmlspecialchars($quiz['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Pytanie</label>
          <select name="question_id" class="form-select" onchange="this.form.submit()" <?= $selectedQuizId ? '' : 'disabled' ?>>
            <?php foreach ($questions as $question): ?>
              <option value="<?= (int) $question['id'] ?>" <?= $selectedQuestionId == $question['id'] ? 'selected' : '' ?>><?= htmlspecialchars(mb_strimwidth($question['text'], 0, 80, '...')) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
  </div>

  <?php if (!$selectedQuizId): ?>
    <div class="alert alert-info">Brak quizów. Dodaj quiz, aby dodać odpowiedzi.</div>
  <?php elseif (!$selectedQuestionId): ?>
    <div class="alert alert-info">Brak pytań w wybranym quizie. Dodaj pytanie, aby zarządzać odpowiedziami.</div>
  <?php else: ?>
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Dodaj odpowiedź</h5>
        <form method="post" action="index.php?page=admin/actions" class="row g-3">
          <input type="hidden" name="action" value="create_answer">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="redirect" value="index.php?page=admin/answers&quiz_id=<?= (int) $selectedQuizId ?>&question_id=<?= (int) $selectedQuestionId ?>">
          <input type="hidden" name="question_id" value="<?= (int) $selectedQuestionId ?>">
          <div class="col-md-8">
            <label class="form-label">Treść odpowiedzi</label>
            <textarea name="content" class="form-control" rows="2" required></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label">Punktacja</label>
            <input type="number" name="weight" class="form-control" value="0">
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Zapisz odpowiedź</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Lista odpowiedzi</h5>
        <?php if (empty($answers)): ?>
          <p class="text-muted mb-0">Brak odpowiedzi dla tego pytania.</p>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($answers as $answer): ?>
              <div class="list-group-item">
                <form method="post" action="index.php?page=admin/actions" class="row g-3 align-items-end">
                  <input type="hidden" name="action" value="update_answer">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                  <input type="hidden" name="redirect" value="index.php?page=admin/answers&quiz_id=<?= (int) $selectedQuizId ?>&question_id=<?= (int) $selectedQuestionId ?>">
                  <input type="hidden" name="answer_id" value="<?= (int) $answer['id'] ?>">
                  <div class="col-md-8">
                    <label class="form-label">Treść odpowiedzi</label>
                    <textarea name="content" class="form-control" rows="2" required><?= htmlspecialchars($answer['content']) ?></textarea>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Punktacja</label>
                    <input type="number" name="weight" class="form-control" value="<?= (int) $answer['weight'] ?>">
                  </div>
                  <div class="col-md-2 d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-primary w-100">Zapisz</button>
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
