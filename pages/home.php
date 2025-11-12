<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php'; // dostosuj ścieżkę jeśli potrzeba

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['password'];

    $stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'quiz_password_hash' LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($input, $row['value'])) {
        $_SESSION['quiz_access'] = true;
        header('Location: index.php?page=quiz');
        exit;
    } else {
        $error = "Nieprawidłowe hasło.";
    }
}
?>

<main class="container my-5 position-relative z-3">
  <div class="row g-4">
    <div class="col-12">
      <div class="card shadow bg-white p-5 card-custom w-100 align-items-center justify-content-center">
        <div class="mb-4 text-center align-items-center justify-content-center">
          <img src="assets/img/full_logo.svg" alt="in2grow logo" height="80">
        </div>
        <p class="text-muted small mb-2">
          Każde pytanie składa się z czterech odpowiedzi. Każda z nich składa się z czterech głównych haseł, definiowanych szeregiem dodatkowych słów. Spośród tych czterech haseł - nawet, jeśli żadne z pojęć Ciebie nie charakteryzują zbyt dokładnie - wybierz to, które jest najbliższe.
        </p>
        <p class="text-muted small mb-3">
          Staraj się wybierać te odpowiedzi, które najbardziej Ciebie charakteryzują – taki jestem najczęściej, w większości sytuacji, w większości kontaktów z innymi, lub podczas większości zadań, które wykonuję.
        </p>

        <?php if ($error): ?>
          <p class="text-danger fw-semibold mt-3"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" class="mt-3 home-password-wrapper">
  <input type="password" name="password" class="form-control mb-3 home-password-field" placeholder="Hasło" required>
  <button type="submit" class="btn btn-primary px-4">Rozpocznij test</button>
</form>
      </div>
    </div>
  </div>
</main>
