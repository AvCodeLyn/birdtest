<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php?page=login');
    exit;
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/questions_repository.php';

seedQuizQuestionsIfEmpty($conn);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_question'])) {
        $id = (int) ($_POST['question_id'] ?? 0);
        deleteQuizQuestion($conn, $id);
        $_SESSION['flash'] = 'Pytanie zostało usunięte.';
        header('Location: index.php?page=questions');
        exit;
    }

    $questionOrder = max(1, (int) ($_POST['question_order'] ?? 0));
    $options = buildOptionsPayload($_POST);

    if (count($options) < 4) {
        $errors[] = 'Uzupełnij wszystkie cztery odpowiedzi.';
    }

    if (empty($errors)) {
        upsertQuizQuestion($conn, [
            'id' => isset($_POST['question_id']) ? (int) $_POST['question_id'] : null,
            'order' => $questionOrder,
            'options' => $options,
        ]);

        $_SESSION['flash'] = isset($_POST['question_id']) && $_POST['question_id'] !== ''
            ? 'Pytanie zostało zaktualizowane.'
            : 'Pytanie zostało dodane.';

        header('Location: index.php?page=questions');
        exit;
    }
}

$search = trim($_GET['search'] ?? '');
$perPage = (int) ($_GET['per_page'] ?? 5);
$perPage = max(1, min($perPage, 25));
$currentPage = max(1, (int) ($_GET['p'] ?? 1));
$total = countQuizQuestions($conn, $search ?: null);
$totalPages = max(1, (int) ceil($total / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$questions = fetchQuizQuestions($conn, $search ?: null, $perPage, $offset);

function mapOptions(array $options): array
{
    $mapped = ['D' => '', 'I' => '', 'S' => '', 'C' => ''];
    foreach ($options as $option) {
        $mapped[$option['label']] = $option['text'];
    }
    return $mapped;
}
?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-4 z-3" role="alert" style="min-width:300px;">
    <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zamknij"></button>
  </div>
<?php endif; ?>

<main class="container py-5 admin-questions">
  <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 mb-1">Bank pytań</h1>
      <p class="text-muted mb-0">Zarządzaj pytaniami i odpowiedziami, korzystając z tabeli, modali lub edycji inline.</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#questionModal">Dodaj pytanie</button>
    </div>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <div><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form class="card shadow-sm mb-4" method="get" action="index.php">
    <input type="hidden" name="page" value="questions">
    <div class="card-body d-flex flex-column flex-lg-row gap-3 align-items-lg-end justify-content-between">
      <div class="flex-grow-1">
        <label for="search" class="form-label">Filtruj po treści odpowiedzi</label>
        <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="np. ambitny, kreatywny...">
      </div>
      <div>
        <label for="perPage" class="form-label">Na stronę</label>
        <select class="form-select" id="perPage" name="per_page">
          <?php foreach ([5, 10, 15, 20, 25] as $size): ?>
            <option value="<?= $size ?>" <?= $perPage === $size ? 'selected' : '' ?>><?= $size ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label d-block">&nbsp;</label>
        <button type="submit" class="btn btn-outline-secondary">Filtruj</button>
      </div>
    </div>
  </form>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:90px;">Kolejność</th>
              <th>Odpowiedź D</th>
              <th>Odpowiedź I</th>
              <th>Odpowiedź S</th>
              <th>Odpowiedź C</th>
              <th class="text-end" style="width:220px;">Akcje</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($questions as $question): ?>
              <?php $mapped = mapOptions($question['options']); ?>
              <tr class="question-row" data-id="<?= $question['id'] ?>">
                <td>
                  <span class="cell-display fw-semibold">#<?= $question['order'] ?></span>
                  <input type="number" class="form-control form-control-sm cell-edit d-none" value="<?= $question['order'] ?>" min="1">
                </td>
                <?php foreach (['D', 'I', 'S', 'C'] as $label): ?>
                  <td>
                    <div class="cell-display small text-muted"><?= htmlspecialchars($mapped[$label]) ?></div>
                    <textarea class="form-control form-control-sm cell-edit d-none" rows="2"><?= htmlspecialchars($mapped[$label]) ?></textarea>
                  </td>
                <?php endforeach; ?>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-2 flex-wrap action-defaults">
                    <button type="button" class="btn btn-sm btn-outline-primary open-edit-modal"
                            data-id="<?= $question['id'] ?>"
                            data-order="<?= $question['order'] ?>"
                            data-d="<?= htmlspecialchars($mapped['D']) ?>"
                            data-i="<?= htmlspecialchars($mapped['I']) ?>"
                            data-s="<?= htmlspecialchars($mapped['S']) ?>"
                            data-c="<?= htmlspecialchars($mapped['C']) ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#questionModal">
                      Edytuj (modal)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary inline-edit-btn">Edytuj inline</button>
                    <form method="post" class="d-inline" onsubmit="return confirm('Czy na pewno usunąć to pytanie?');">
                      <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                      <input type="hidden" name="delete_question" value="1">
                      <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                    </form>
                  </div>
                  <div class="inline-actions d-none d-flex justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-sm btn-success save-inline">Zapisz</button>
                    <button type="button" class="btn btn-sm btn-link cancel-inline">Anuluj</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($questions)): ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Brak wyników dla podanego filtra.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php if ($totalPages > 1): ?>
      <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="text-muted small">Strona <?= $currentPage ?> z <?= $totalPages ?> (<?= $total ?> pytań)</span>
        <div class="pagination mb-0">
          <ul class="pagination mb-0">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <?php
                $params = http_build_query([
                  'page' => 'questions',
                  'p' => $p,
                  'per_page' => $perPage,
                  'search' => $search,
                ]);
              ?>
              <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="index.php?<?= $params ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<!-- Modal dodawania/edycji -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="questionModalForm">
        <div class="modal-header">
          <h5 class="modal-title" id="questionModalLabel">Dodaj pytanie</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="question_id" id="modalQuestionId">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label">Kolejność na liście</label>
              <input type="number" class="form-control" name="question_order" id="modalQuestionOrder" min="1" required>
            </div>
            <div class="col-12">
              <div class="row g-3">
                <?php foreach (['D' => 'Dominacja (D)', 'I' => 'Inicjatywa (I)', 'S' => 'Stabilność (S)', 'C' => 'Sumienność (C)'] as $label => $title): ?>
                  <div class="col-12 col-md-6">
                    <label class="form-label"><?= $title ?></label>
                    <textarea class="form-control" name="option_<?= strtolower($label) ?>" rows="3" required></textarea>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Anuluj</button>
          <button type="submit" name="save_question" value="1" class="btn btn-primary">Zapisz</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const questionModal = document.getElementById('questionModal');
  const modalTitle = document.getElementById('questionModalLabel');
  const modalId = document.getElementById('modalQuestionId');
  const modalOrder = document.getElementById('modalQuestionOrder');

  questionModal?.addEventListener('show.bs.modal', (event) => {
    const trigger = event.relatedTarget;
    const isEdit = trigger?.classList.contains('open-edit-modal');

    modalTitle.textContent = isEdit ? 'Edytuj pytanie' : 'Dodaj pytanie';
    modalId.value = isEdit ? trigger.dataset.id : '';
    modalOrder.value = isEdit ? trigger.dataset.order : '';

    ['d', 'i', 's', 'c'].forEach((key) => {
      const textarea = questionModal.querySelector(`[name="option_${key}"]`);
      textarea.value = isEdit ? (trigger.dataset[key] || '') : '';
    });
  });

  document.querySelectorAll('.inline-edit-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.question-row');
      row.classList.add('editing');
      row.querySelectorAll('.cell-display').forEach(el => el.classList.add('d-none'));
      row.querySelectorAll('.cell-edit').forEach(el => el.classList.remove('d-none'));
      row.querySelector('.inline-actions').classList.remove('d-none');
      row.querySelector('.action-defaults').classList.add('d-none');
    });
  });

  document.querySelectorAll('.cancel-inline').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.question-row');
      row.classList.remove('editing');
      row.querySelectorAll('.cell-display').forEach(el => el.classList.remove('d-none'));
      row.querySelectorAll('.cell-edit').forEach(el => el.classList.add('d-none'));
      row.querySelector('.inline-actions').classList.add('d-none');
      row.querySelector('.action-defaults').classList.remove('d-none');
    });
  });

  document.querySelectorAll('.save-inline').forEach((btn) => {
    btn.addEventListener('click', () => {
      const row = btn.closest('.question-row');
      const id = row.dataset.id;
      const order = row.querySelector('input[type="number"]').value;
      const textareas = row.querySelectorAll('textarea');
      const payload = new FormData();
      payload.append('save_question', '1');
      payload.append('question_id', id);
      payload.append('question_order', order);
      const labels = ['d', 'i', 's', 'c'];
      textareas.forEach((textarea, index) => {
        payload.append(`option_${labels[index]}`, textarea.value);
      });

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'index.php?page=questions';
      form.classList.add('d-none');
      document.body.appendChild(form);

      for (const [key, value] of payload.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
      }

      form.submit();
    });
  });
</script>
