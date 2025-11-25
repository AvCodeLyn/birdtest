<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = new Auth($conn);
$auth->requireRole('admin');

// Obsługa zmiany hasła
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_quiz_password'])) {
    $newPassword = $_POST['new_password'];
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'quiz_password_hash'");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $stmt->close();

    $_SESSION['flash'] = "Hasło do testu zostało zmienione.";
    header("Location: index.php?page=allresults");
    exit;
}

// Obsługa czyszczenia danych
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_all'])) {
    $conn->query("DELETE FROM quiz_results");
    $_SESSION['flash'] = "Wszystkie odpowiedzi zostały usunięte.";
    header("Location: index.php?page=allresults");
    exit;
}

// Pobierz dane do macierzy
$result = $conn->query("SELECT * FROM quiz_results ORDER BY created_at DESC");

$map = [
    'Orzeł'  => 'orzel',
    'Papuga' => 'papuga',
    'Gołąb'  => 'golab',
    'Sowa'   => 'sowa'
];

$countMatrix = ['orzel' => 0, 'papuga' => 0, 'golab' => 0, 'sowa' => 0];

while ($row = $result->fetch_assoc()) {
    $birds = explode(', ', $row['dominant_birds']);
    foreach ($birds as $birdName) {
        $key = $map[$birdName] ?? null;
        if ($key !== null) {
            $countMatrix[$key]++;
        }
    }
}

$conn->close();


?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-4 z-3" role="alert" style="min-width:300px;">
    <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Zamknij"></button>
  </div>
<?php endif; ?>

<section class="result-section" style="background-color:#2a1b3d; color:white; min-height:100vh; display:flex; align-items:center; justify-content:center;">
  <div class="container">
    <div class="row justify-content-center align-items-center">
      <!-- Lewa kolumna -->
      <div class="col-12 col-md-2 d-flex flex-column align-items-center justify-content-between" style="height:500px;">
        <div class="d-flex flex-column align-items-center mb-4">
          <video class="bird-video" autoplay muted loop playsinline style="width:110px; height:110px;">
            <source src="assets/video/owl_loop.mp4" type="video/mp4">
          </video>
          <div class="fw-bold mt-3" style="font-size:1.6rem;">Sowa</div>
          <div class="display-3" style="font-weight:bold;"><?= $countMatrix['sowa'] ?></div>
        </div>
        <div class="d-flex flex-column align-items-center mt-4">
          <video class="bird-video" autoplay muted loop playsinline style="width:110px; height:110px;">
            <source src="assets/video/dove_loop.mp4" type="video/mp4">
          </video>
          <div class="fw-bold mt-3" style="font-size:1.6rem;">Gołąb</div>
          <div class="display-3" style="font-weight:bold;"><?= $countMatrix['golab'] ?></div>
        </div>
      </div>
      <!-- Środek: macierz -->
      <div class="col-12 col-md-8 d-flex justify-content-center">
        <div style="width:480px; aspect-ratio:1/1; display:grid; grid-template-columns:1fr 1fr; grid-template-rows:1fr 1fr; gap:6px;">
          <div style="background:linear-gradient(to bottom, #90CAF9, #42A5F5); position:relative; border-radius:18px;">
            <strong style="position:absolute;top:16px;left:16px;color:white;font-size:1.4rem;">Sowa</strong>
            <?php for ($i = 0; $i < $countMatrix['sowa']; $i++): ?>
              <img src="assets/img/sowa.svg" style="width:48px;position:absolute;top:<?= rand(15,85) ?>%;left:<?= rand(10,90) ?>%;transform:translate(-50%,-50%);">
            <?php endfor; ?>
          </div>
          <div style="background:linear-gradient(to bottom, #FF5252, #D32F2F); position:relative; border-radius:18px;">
            <strong style="position:absolute;top:16px;right:16px;color:white;font-size:1.4rem;">Orzeł</strong>
            <?php for ($i = 0; $i < $countMatrix['orzel']; $i++): ?>
              <img src="assets/img/orzel.svg" style="width:48px;position:absolute;top:<?= rand(15,85) ?>%;left:<?= rand(10,90) ?>%;transform:translate(-50%,-50%);">
            <?php endfor; ?>
          </div>
          <div style="background:linear-gradient(to bottom, #C5E1A5, #81C784); position:relative; border-radius:18px;">
            <strong style="position:absolute;bottom:16px;left:16px;color:white;font-size:1.4rem;">Gołąb</strong>
            <?php for ($i = 0; $i < $countMatrix['golab']; $i++): ?>
              <img src="assets/img/golab.svg" style="width:48px;position:absolute;top:<?= rand(15,85) ?>%;left:<?= rand(10,90) ?>%;transform:translate(-50%,-50%);">
            <?php endfor; ?>
          </div>
          <div style="background:linear-gradient(to bottom, #FFF176, #FFD54F); position:relative; border-radius:18px;">
            <strong style="position:absolute;bottom:16px;right:16px;color:white;font-size:1.4rem;">Papuga</strong>
            <?php for ($i = 0; $i < $countMatrix['papuga']; $i++): ?>
              <img src="assets/img/papuga.svg" style="width:48px;position:absolute;top:<?= rand(15,85) ?>%;left:<?= rand(10,90) ?>%;transform:translate(-50%,-50%);">
            <?php endfor; ?>
          </div>
        </div>
      </div>
      <!-- Prawa kolumna -->
      <div class="col-12 col-md-2 d-flex flex-column align-items-center justify-content-between" style="height:500px;">
        <div class="d-flex flex-column align-items-center mb-4">
          <video class="bird-video" autoplay muted loop playsinline style="width:110px; height:110px;">
            <source src="assets/video/eagle_loop.mp4" type="video/mp4">
          </video>
          <div class="fw-bold mt-3" style="font-size:1.6rem;">Orzeł</div>
          <div class="display-3" style="font-weight:bold;"><?= $countMatrix['orzel'] ?></div>
        </div>
        <div class="d-flex flex-column align-items-center mt-4">
          <video class="bird-video" autoplay muted loop playsinline style="width:110px; height:110px;">
            <source src="assets/video/parrot_loop.mp4" type="video/mp4">
          </video>
          <div class="fw-bold mt-3" style="font-size:1.6rem;">Papuga</div>
          <div class="display-3" style="font-weight:bold;"><?= $countMatrix['papuga'] ?></div>
        </div>
      </div>
    </div>
  </div>
  
</section>

<!-- Ukryty formularz do zmiany hasła -->
<form id="changePasswordForm" method="post" action="index.php?page=allresults" style="display:none;">
  <input type="hidden" name="new_password" id="hiddenNewPassword">
  <input type="hidden" name="change_quiz_password" value="1">
</form>




