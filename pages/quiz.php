<?php
if (!isset($_SESSION['quiz_access'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/QuizRepository.php';

try {
    $pdo = getPdoConnection();
    $quizRepository = new QuizRepository($pdo);
    $activeQuiz = $quizRepository->getActiveQuiz();

    if (!$activeQuiz) {
        echo '<p class="text-danger text-center mt-4">Brak aktywnego quizu do wyświetlenia.</p>';
        return;
    }

    $pytania = $quizRepository->getQuestionsWithAnswers((int) $activeQuiz['id']);

    if (empty($pytania)) {
        echo '<p class="text-danger text-center mt-4">Brak pytań w aktualnym quizie.</p>';
        return;
    }
} catch (Throwable $e) {
    echo '<p class="text-danger text-center mt-4">Wystąpił błąd podczas ładowania quizu: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '</p>';
    return;
}
?>

<main class="container-fluid p-0">
  <div class="quiz-container">
    <form method="POST" action="index.php?page=result" id="quizForm">
      <?php foreach ($pytania as $i => $pytanie): ?>
        <div class="question-card <?= $i === 0 ? 'active animate-in' : '' ?>" data-index="<?= $i ?>">
          <div class="d-grid gap-3">
            <?php foreach ($pytanie['answers'] as $litera => $tekst): ?>
              <button type="button" class="btn btn-outline-secondary answer-btn" data-value="<?= $litera ?>">
                <?= $tekst ?>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="q<?= $i + 1 ?>" value="">
          <button type="button" class="btn btn-primary mt-4 confirm-btn" disabled>Zatwierdź</button>
        </div>
      <?php endforeach; ?>
    </form>
  </div>
</main>

<script>
  const cards = document.querySelectorAll('.question-card');
  cards.forEach((card, index) => {
    const answerButtons = card.querySelectorAll('.answer-btn');
    const confirmBtn = card.querySelector('.confirm-btn');
    const hiddenInput = card.querySelector('input[type="hidden"]');

    answerButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        answerButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        hiddenInput.value = btn.dataset.value;
        confirmBtn.disabled = false;
      });
    });

    confirmBtn.addEventListener('click', () => {
      card.classList.add('animate-out');
      setTimeout(() => {
        card.classList.remove('active', 'animate-in', 'animate-out');
        if (index + 1 < cards.length) {
          const next = cards[index + 1];
          next.classList.add('active', 'animate-in');
        } else {
          document.getElementById('quizForm').submit();
        }
      }, 400);
    });
  });
</script>
