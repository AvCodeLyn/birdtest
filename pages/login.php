<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = new Auth($conn);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $haslo = (string) filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

    if ($auth->login($login, $haslo)) {
        $user = $auth->currentUser();
        if ($user && $user['role'] === 'admin') {
            header('Location: index.php?page=allresults');
            exit;
        }

    if ($user && password_verify($haslo, $user['password_hash']) && $user['role'] === 'admin') {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        header('Location: index.php?page=allresults');
        exit;
    } else {
        $error = "Nieprawidłowy login lub hasło.";
    }
}
?>
<div class="container my-5" style = "max-width: 500px;">
  <div class="card shadow bg-white p-5 card-custom w-100 align-items-center justify-content-center">
    <p class="text-muted mb-3">Korzystaj z panelu wyników jedynie na komputerze stacjonarnym. Widok panelu wyników nie jest dostosowany do urządzeń mobilnych!</p>
    <?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <input type="text" class="form-control mb-3 home-password-field" id="username" name="username" placeholder = "Login" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control mb-3 home-password-field" id="password" name="password" placeholder = "Hasło" required >
      </div>
      <button type="submit" class="btn btn-primary w-100">Zaloguj się</button>
    </form>
  </div>
</div>