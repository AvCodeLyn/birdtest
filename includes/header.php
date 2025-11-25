<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bird Test App</title>
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body<?php if (isset($page)) echo ' class="page-' . htmlspecialchars($page) . '"'; ?>>

<!-- PRELOADER -->
<div id="preloader">
  <span class="loader"></span>
</div>

<?php $page = $_GET['page'] ?? ''; ?>

<?php if ($page === ''): ?>
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark z-3">
      <div class="container">
        <a class="navbar-brand" href="index.php">
          <img src="assets/img/logo_white.svg" alt="in2grow" height="60">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavLabel">
              <img src="assets/img/logo_white.svg" alt="in2grow" height="40">
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="navbar-nav d-flex flex-column align-items-start gap-2">
              <li class="nav-item">
                <a href="https://in2grow.pl" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100 mb-2">
                  Odwiedź stronę In2grow
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=allresults" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100">
                  Panel wyników
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
  <!-- HOMEPAGE HERO Z WIDEO -->
  <div class="homepage-hero position-relative">
    <video autoplay muted loop playsinline class="hero-video">
      <source src="assets/video/hero.mp4" type="video/mp4">
      Twoja przeglądarka nie obsługuje wideo.
    </video>

    <div class="hero-overlay"></div>

    

    <div class="hero-content d-flex flex-column align-items-center justify-content-center text-white text-center z-2">
      <h1 class="display-4 fw-bold">Jakim ptakiem jesteś?</h1>
      <p class="lead">Odkryj swój styl zachowania i komunikacji</p>
    </div>
  </div>

<?php elseif ($page === 'login'): ?>
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark z-3">
      <div class="container">
        <a class="navbar-brand" href="index.php">
          <img src="assets/img/logo_white.svg" alt="in2grow" height="60">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavLabel">
              <img src="assets/img/logo_white.svg" alt="in2grow" height="40">
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="navbar-nav d-flex flex-column align-items-start gap-2">
              <li class="nav-item">
                <a href="https://in2grow.pl" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100 mb-2">
                  Odwiedź stronę In2grow
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=allresults" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100">
                  Panel wyników
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
  <!-- HOMEPAGE HERO Z WIDEO -->
  <div class="homepage-hero position-relative">
    <video autoplay muted loop playsinline class="hero-video">
      <source src="assets/video/hero.mp4" type="video/mp4">
      Twoja przeglądarka nie obsługuje wideo.
    </video>

    <div class="hero-overlay"></div>
    
    <div class="hero-content d-flex flex-column align-items-center justify-content-center text-white text-center z-2">
      <h1 class="display-4 fw-bold">Logowanie</h1>
</br>
    </div>
  </div>

<?php elseif ($page === 'quiz'): ?>
  <!-- QUIZ HERO -->
  <div class="hero-section-header" id="quiz-hero">
    <nav class="navbar fixed-top navbar-dark z-3 justify-content-center" style="background: transparent;">
      <div class="container d-flex justify-content-center">
        <a class="navbar-brand mx-auto" href="index.php">
          <img src="assets/img/logo_white.svg" alt="in2grow" height="60">
        </a>
      </div>
    </nav>
    <div class="hero-content d-flex flex-column align-items-center justify-content-center text-white text-center">
      <h1 class="display-5 fw-bold" id="heroTitle">Jakim ptakiem jesteś?</h1>
      <div class="quiz-progress-container w-100 px-4">
        <div class="quiz-progress-bar">
          <div class="quiz-progress-fill" id="quizProgressBar"></div>
        </div>
      </div>
      <p class="lead" id="heroSubtitle">Wybierz zestaw, który najlepiej Cię opisuje</p>
    </div>
  </div>
<?php elseif ($page === 'result'): ?>
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark z-3">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="assets/img/logo_white.svg" alt="in2grow" height="60">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav d-flex align-items-center gap-2">
          <li class="nav-item">
            <a href="https://in2grow.pl" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover">
              Odwiedź stronę In2grow
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php?page=allresults" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover">
              Panel wyników
            </a>
          </li>
        </ul>
      </div>

      <div class="offcanvas offcanvas-end bg-blur-purple" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavLabel">
            <img src="assets/img/logo_white.svg" alt="in2grow" height="40">
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav d-flex flex-column align-items-start gap-2">
            <li class="nav-item">
              <a href="https://in2grow.pl" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100 mb-2">
                Odwiedź stronę In2grow
              </a>
            </li>
            <li class="nav-item">
              <a href="index.php?page=allresults" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100">
                Panel wyników
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
<?php elseif ($page === 'allresults'): ?>
  <nav class="navbar navbar-expand-lg fixed-top navbar-dark z-3">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="assets/img/logo_white.svg" alt="in2grow" height="60">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- MENU DESKTOP -->
      <div class="collapse navbar-collapse justify-content-end d-none d-lg-flex" id="navbarNav">
        <ul class="navbar-nav d-flex align-items-center gap-2">
          <li class="nav-item">
            <a href="#" id="changePasswordBtn" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover">
              Zmień hasło do testu
            </a>
          </li>
          <li class="nav-item">
            <form method="post" action="/actions.php" onsubmit="return confirm('Czy na pewno chcesz wyczyścić wszystkie wyniki?');" class="d-inline">
              <input type="hidden" name="clear_all" value="1">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
              <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 custom-hover">
                Wyczyść wszystko
              </button>
            </form>
          </li>
          <li class="nav-item">
            <a href="index.php?page=logout" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover">
              Wyloguj się
            </a>
          </li>
        </ul>
      </div>
      <!-- MENU MOBILE -->
      <div class="offcanvas offcanvas-end bg-blur-purple d-lg-none" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavLabel">
            <img src="assets/img/logo_white.svg" alt="in2grow" height="40">
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav d-flex flex-column align-items-start gap-2">
            <li class="nav-item w-100">
              <a href="#" id="changePasswordBtnMobile" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100 mb-2">
                Zmień hasło do testu
              </a>
            </li>
            <li class="nav-item w-100">
              <form method="post" action="/actions.php" onsubmit="return confirm('Czy na pewno chcesz wyczyścić wszystkie wyniki?');" class="w-100">
                <input type="hidden" name="clear_all" value="1">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 custom-hover w-100">
                  Wyczyść wszystko
                </button>
              </form>
            </li>
            <li class="nav-item w-100">
              <a href="index.php?page=logout" class="btn btn-outline-light rounded-pill px-4 py-2 custom-hover w-100">
                Wyloguj się
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
<?php endif; ?>
