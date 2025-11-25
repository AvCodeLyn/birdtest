<?php
if (!isset($_SESSION['quiz_access'])) {
    header('Location: index.php');
    exit;
}

include 'db.php';
require_once __DIR__ . '/../includes/questions_repository.php';

seedQuizQuestionsIfEmpty($conn);
$questions = fetchQuizQuestions($conn);
$totalQuestions = count($questions);
?>

<main class="container-fluid p-0">
  <div class="quiz-container">
    <div class="d-flex flex-column gap-3 mb-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-semibold" id="localProgressLabel">Pytanie 1 z <?= $totalQuestions ?></div>
        <div class="small text-muted">Wybierz odpowiedź, aby przejść dalej</div>
      </div>
      <div class="quiz-stepper" aria-label="Postęp pytań">
        <?php foreach ($questions as $index => $question): ?>
          <span class="step-dot <?= $index === 0 ? 'active' : '' ?>" data-step="<?= $index ?>"></span>
        <?php endforeach; ?>
      </div>
    </div>

    <form method="POST" action="index.php?page=result" id="quizForm">
      <?php foreach ($questions as $i => $question): ?>
        <div class="question-card <?= $i === 0 ? 'active animate-in' : '' ?>" data-index="<?= $i ?>">
          <div class="d-grid gap-3">
            <?php foreach ($question['options'] as $option): ?>
              <button type="button" class="btn btn-outline-secondary answer-btn" data-value="<?= $option['label'] ?>">
                <?= htmlspecialchars($option['text']) ?>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="q<?= $i + 1 ?>" value="">
          <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mt-4 gap-3">
            <div class="text-danger small fw-semibold validation-message d-none">Wybierz jedną odpowiedź, zanim przejdziesz dalej.</div>
            <button type="button" class="btn btn-primary ms-sm-auto confirm-btn" disabled>Dalej</button>
          </div>
        </div>
      <?php endforeach; ?>
    </form>
  </div>
</main>

<script>
  const cards = document.querySelectorAll('.question-card');
  const stepDots = document.querySelectorAll('.step-dot');
  const localProgressLabel = document.getElementById('localProgressLabel');
  const totalQuestions = cards.length;

  const dispatchProgressChange = (index) => {
    window.latestQuizProgress = { index, total: totalQuestions };
    window.dispatchEvent(new CustomEvent('quiz-progress-change', {
      detail: window.latestQuizProgress
    }));
  };

  const updateStepper = (activeIndex) => {
    const percent = totalQuestions ? Math.round(((activeIndex + 1) / totalQuestions) * 100) : 0;
    localProgressLabel.textContent = `Pytanie ${activeIndex + 1} z ${totalQuestions}`;

    stepDots.forEach((dot, idx) => {
      dot.classList.toggle('active', idx === activeIndex);
      dot.classList.toggle('completed', idx < activeIndex);
      if (idx <= activeIndex) {
        dot.style.setProperty('--progress', `${percent}%`);
      }
    });

    dispatchProgressChange(activeIndex);
  };

  cards.forEach((card, index) => {
    const answerButtons = card.querySelectorAll('.answer-btn');
    const confirmBtn = card.querySelector('.confirm-btn');
    const hiddenInput = card.querySelector('input[type="hidden"]');
    const validation = card.querySelector('.validation-message');

    answerButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        answerButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        hiddenInput.value = btn.dataset.value;
        confirmBtn.disabled = false;
        validation?.classList.add('d-none');
      });
    });

    confirmBtn.addEventListener('click', () => {
      if (!hiddenInput.value) {
        validation?.classList.remove('d-none');
        confirmBtn.disabled = true;
        return;
      }

      card.classList.add('animate-out');
      setTimeout(() => {
        card.classList.remove('active', 'animate-in', 'animate-out');
        if (index + 1 < cards.length) {
          const next = cards[index + 1];
          next.classList.add('active', 'animate-in');
          updateStepper(index + 1);
        } else {
          document.getElementById('quizForm').submit();
        }
      }, 400);
    });
  });

  updateStepper(0);
</script>
