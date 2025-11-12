<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $map = ['D' => 'orzel', 'I' => 'papuga', 'S' => 'golab', 'C' => 'sowa'];
    $count = ['orzel' => 0, 'papuga' => 0, 'golab' => 0, 'sowa' => 0];

    foreach ($_POST as $val) {
        $ptak = $map[$val];
        $count[$ptak]++;
    }

    $max = max($count);
    $dominant = array_keys($count, $max);

    $birdLabels = [
        'orzel' => 'Orzeł',
        'papuga' => 'Papuga',
        'golab' => 'Gołąb',
        'sowa' => 'Sowa'
    ];
    $dominant_text = implode(', ', array_map(fn($b) => $birdLabels[$b], $dominant));

    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO quiz_results (ip_address, orzel, papuga, golab, sowa, dominant_birds) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiis", $ip, $count['orzel'], $count['papuga'], $count['golab'], $count['sowa'], $dominant_text);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $_SESSION['quiz_result'] = [
        'count' => $count,
        'dominant' => $dominant,
        'dominant_text' => $dominant_text
    ];

    header('Location: index.php?page=result');
    exit;
}

if (!isset($_SESSION['quiz_result'])) {
    header('Location: index.php?page=quiz');
    exit;
}

extract($_SESSION['quiz_result']);
unset($_SESSION['quiz_result']);

$birdLabels = [
    'orzel' => 'Orzeł',
    'papuga' => 'Papuga',
    'golab' => 'Gołąb',
    'sowa' => 'Sowa'
];
$descriptions = [
    'orzel' => 'Orzeł to urodzony lider – ambitny, zdecydowany, nastawiony na cel. Często przejmuje inicjatywę.',
    'papuga' => 'Papuga to dusza towarzystwa – optymistyczna, entuzjastyczna, kreatywna i ekspresyjna.',
    'golab'  => 'Gołąb to spokojny mediator – lojalny, pomocny, zrównoważony i oddany innym.',
    'sowa'   => 'Sowa to analityk – dokładna, logiczna, ostrożna i zorganizowana.'
];
$gradients = [
    'orzel' => 'gradient-orzel',
    'papuga' => 'gradient-papuga',
    'golab' => 'gradient-golab',
    'sowa' => 'gradient-sowa'
];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Wynik quizu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      scroll-behavior: smooth;
    }

    .result-wrapper {
      scroll-snap-type: y mandatory;
      overflow-y: scroll;
      height: 100vh;
    }

    .result-section {
      scroll-snap-align: start;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 2rem;
      background-color: #2a1b3d;
      color: white;
      text-align: center;
    }

    .chart-wrapper {
      width: 80%;
      max-width: 600px;
      height: 300px;
    }

    .bird-colored {
      width: 100px;
      height: 100px;
      margin: 0 auto 1rem auto;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      mask-size: contain;
      mask-repeat: no-repeat;
      mask-position: center;
      -webkit-mask-size: contain;
      -webkit-mask-repeat: no-repeat;
      -webkit-mask-position: center;
    }

    .gradient-orzel { background: linear-gradient(to bottom, #FF5252, #D32F2F); }
    .gradient-papuga { background: linear-gradient(to bottom, #FFF176, #FFD54F); }
    .gradient-golab { background: linear-gradient(to bottom, #C5E1A5, #81C784); }
    .gradient-sowa { background: linear-gradient(to bottom, #90CAF9, #42A5F5); }
  </style>
</head>
<body>

<div class="result-wrapper" id="resultWrapper">

  <section class="result-section">
    <h2 class="display-5 mb-4">Twój ptak: <?= $dominant_text ?></h2>
    <div class="row justify-content-center g-4">
      <?php foreach ($dominant as $bird): ?>
        <div class="col-md-auto col-6 d-flex flex-column align-items-center">
<video class="bird-video" autoplay muted loop playsinline>
  <source src="assets/video/<?= match($bird) {
    'orzel' => 'eagle_loop.mp4',
    'papuga' => 'parrot_loop.mp4',
    'golab' => 'dove_loop.mp4',
    'sowa' => 'owl_loop.mp4'
  } ?>" type="video/mp4">
  Twoja przeglądarka nie obsługuje wideo.
</video>
          <h5 class="mt-3"><?= $birdLabels[$bird] ?></h5>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Poznaj szczegóły przewijając w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <h3 class="mb-4">Twój wynik na wykresie</h3>
    <div class="chart-wrapper">
      <canvas id="chart"></canvas>
    </div>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Przewiń w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <h3 class="mb-4 w-100 text-center">Opis wszystkich stylów vs PRESJA</h3>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Przewiń w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <h3 class="mb-4">ORZEŁ - lider z wysoko uniesioną głową</h3>
    <div class="d-flex flex-column align-items-center">
      <video class="bird-video mb-3" autoplay muted loop playsinline style="max-width:180px;">
        <source src="assets/video/eagle_loop.mp4" type="video/mp4">
        Twoja przeglądarka nie obsługuje wideo.
      </video>
      <p class="small text-white text-start" style="max-width:600px">
        To urodzony zdobywca. Wie, czego chce, i nie traci czasu - cel ma być osiągnięty, najlepiej teraz, zaraz. Orzeł działa z rozmachem, pewnie i bezkompromisowo. Uwielbia wyzwania, bo to one rozpalają w nim ogień - im trudniej, tym lepiej. Kontrola i efektywność to jego znaki rozpoznawcze.<br><br>
        <b>Gdy presja rośnie</b>, Orzeł nie cofa się - on atakuje. Może stać się niecierpliwy, a nawet zbyt dominujący, lekceważąc emocje innych i ryzyko. W jego głowie rzadko pojawia się myśl: „Może ktoś miał powód…” - raczej: „Nie zrobił, bo mu się nie chciało”.<br><br>
        <b>Typowe błędy atrybucyjne:</b><br>
        - Podstawowy błąd atrybucji: „Nie zrobił tego, bo jest leniwy”<br>
        - Etykietowanie: „Ten zespół to banda nieudaczników”<br>
        - Mało tolerancji dla zewnętrznych przyczyn - częściej stosuje wewnętrzne atrybucje wobec innych<br><br>
        <b>Strategie radzenia sobie:</b><br>
        - Zatrzymaj się i przeanalizuj sytuację - zanim podejmiesz działanie<br>
        - Rozwijaj empatię i pytaj o intencje innych - unikniesz pochopnych ocen<br>
        - Ucz się dostrzegać wpływ sytuacji, nie tylko ludzi
      </p>
    </div>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Przewiń w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <h3 class="mb-4">PAPUGA - dusza zespołu, iskra towarzystwa</h3>
    <div class="d-flex flex-column align-items-center">
      <video class="bird-video mb-3" autoplay muted loop playsinline style="max-width:180px;">
        <source src="assets/video/parrot_loop.mp4" type="video/mp4">
        Twoja przeglądarka nie obsługuje wideo.
      </video>
      <p class="small text-white text-start" style="max-width:600px">
        Wnosi kolory, energię i śmiech wszędzie, gdzie się pojawi. Papuga zaraża entuzjazmem, inspiruje, uwielbia być w centrum uwagi. Błyskawicznie nawiązuje relacje, a jej motorem napędowym są emocje  głównie te pozytywne.<br><br>
        <b>Pod presją</b> bywa jednak rozkojarzona. Potrafi zbagatelizować problem, byle tylko nie popsuć atmosfery. Czasem odbiera brak zaproszenia jako odrzucenie, a spojrzenie szefa jako znak: „Pewnie mnie nie lubi”.<br><br>
        <b>Typowe błędy atrybucyjne:</b><br>
        - Personalizacja: „Nie zaprosili mnie - pewnie mnie nie lubią”<br>
        - Czytanie w myślach: „Oni myślą, że się nie nadaję”<br>
        - Często zewnętrzne i stałe atrybucje wobec siebie („Zawsze coś pójdzie nie tak…”)<br><br>
        <b>Strategie radzenia sobie:</b><br>
        - Naucz się stawiać granice i mówić "nie", nawet jeśli boisz się utraty aprobaty<br>
        - Praktykuj realistyczne myślenie: oddziel emocje od faktów<br>
        - Pamiętaj, że nie każdy sygnał to ocena Twojej osoby
      </p>
    </div>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Przewiń w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <h3 class="mb-4">GOŁĄB - spokojna siła, która łączy ludzi</h3>
    <div class="d-flex flex-column align-items-center">
      <video class="bird-video mb-3" autoplay muted loop playsinline style="max-width:180px;">
        <source src="assets/video/dove_loop.mp4" type="video/mp4">
        Twoja przeglądarka nie obsługuje wideo.
      </video>
      <p class="small text-white text-start" style="max-width:600px">
        To uosobienie cierpliwości, lojalności i spokoju. Gołąb ceni przewidywalność, nie szuka poklasku - woli działać w tle, wspierać innych i dbać o harmonię. Tworzy atmosferę bezpieczeństwa, w której inni czują się dobrze.<br><br>
        <b>Gdy pojawia się presja</b>, Gołąb często się wycofuje. Nie lubi konfliktów, więc raczej milknie niż walczy. Może tłumić emocje, próbując po prostu „przetrwać burzę”. A w myślach pojawiają się zdania typu: „I tak mi się nie uda” albo „Zawsze wszystko psuję”.<br><br>
        <b>Typowe błędy atrybucyjne:</b><br>
        - Wyuczona bezradność: „Nie dam rady, nie ma sensu próbować”<br>
        - Myślenie czarno-białe: „Albo wszystko będzie dobrze, albo totalna porażka”<br>
        - Wobec siebie - wewnętrzne i stałe atrybucje („Zawsze to ja zawalam”)<br><br>
        <b>Strategie radzenia sobie:</b><br>
        - Naucz się komunikować swoje potrzeby oraz być asertywnym<br>
        - Zastanów się, czy Twoje myśli są faktami, czy tylko interpretacją<br>
        - Rozwijaj elastyczność - świat nie zawsze musi być przewidywalny
      </p>
    </div>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Przewiń w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <h3 class="mb-4">SOWA - mądrość, precyzja i analityczna głębia</h3>
    <div class="d-flex flex-column align-items-center">
      <video class="bird-video mb-3" autoplay muted loop playsinline style="max-width:180px;">
        <source src="assets/video/owl_loop.mp4" type="video/mp4">
        Twoja przeglądarka nie obsługuje wideo.
      </video>
      <p class="small text-white text-start" style="max-width:600px">
        Sowa to ekspert - dokładna, rozważna, logiczna. Zanim podejmie decyzję, musi mieć dane. Ceni jakość, standardy i porządek. Lubi, kiedy wszystko ma sens - bo wtedy świat staje się przewidywalny i bezpieczny.<br><br>
        <b>Pod presją</b> potrafi jednak ugrzęznąć w analizie. W głowie pojawia się tysiąc scenariuszy - zwykle najgorszych. Każdy błąd urasta do rangi katastrofy, a perfekcjonizm nie pozwala działać, dopóki wszystko nie jest idealne.<br><br>
        <b>Typowe błędy atrybucyjne:</b><br>
        - Katastrofizowanie: „Jeśli się pomylę, wszystko się zawali”<br>
        - Perfekcjonizm: „Nie zrobiłem tego idealnie, więc to porażka”<br>
        - Silna tendencja do wewnętrznych, stałych atrybucji wobec siebie<br><br>
        <b>Strategie radzenia sobie:</b><br>
        - Naucz się działać mimo niepełnych informacji<br>
        - Stosuj zasadę „wystarczająco dobrze” (good enough vs. perfect)<br>
        - Pracuj nad rozróżnianiem faktów od lękowych prognoz
      </p>
    </div>
    <div class="d-flex flex-column align-items-center mt-5">
      <span class="text-white-50 mb-2" style="font-size:1.1rem; letter-spacing:0.5px;">
        Przewiń w dół
      </span>
      <span class="animate-bounce" style="font-size:2rem; color:#fff; opacity:0.7;">&#8595;</span>
    </div>
    <style>
      .animate-bounce {
        display: inline-block;
        animation: bounce 1.2s infinite;
      }
      @keyframes bounce {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(12px);}
      }
    </style>
  </section>

  <section class="result-section">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:40vh;">
      <h4 class="mb-4">Co dalej?</h4>
      <div class="d-flex gap-3 flex-wrap justify-content-center">
        <button type="button" class="btn btn-outline-light btn-lg" onclick="scrollToTop();">
          &#8679; Wróć na górę
        </button>
        <a href="index.php" class="btn btn-primary btn-lg">
          Strona główna
        </a>
      </div>
    </div>
  </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chart').getContext('2d');

const redGradient = ctx.createLinearGradient(0, 0, 0, 400);
redGradient.addColorStop(0, '#FF5252');
redGradient.addColorStop(1, '#D32F2F');

const yellowGradient = ctx.createLinearGradient(0, 0, 0, 400);
yellowGradient.addColorStop(0, '#FFF176');
yellowGradient.addColorStop(1, '#FFD54F');

const greenGradient = ctx.createLinearGradient(0, 0, 0, 400);
greenGradient.addColorStop(0, '#C5E1A5');
greenGradient.addColorStop(1, '#81C784');

const blueGradient = ctx.createLinearGradient(0, 0, 0, 400);
blueGradient.addColorStop(0, '#90CAF9');
blueGradient.addColorStop(1, '#42A5F5');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Orzeł', 'Papuga', 'Gołąb', 'Sowa'],
    datasets: [{
      label: 'Twój wynik',
      data: [<?= $count['orzel'] ?>, <?= $count['papuga'] ?>, <?= $count['golab'] ?>, <?= $count['sowa'] ?>],
      backgroundColor: [redGradient, yellowGradient, greenGradient, blueGradient],
      borderRadius: 10
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        titleColor: '#000',
        bodyColor: '#000',
        backgroundColor: '#fff'
      }
    },
    scales: {
      x: {
        ticks: { color: 'white', font: { weight: 'bold' } },
        grid: { color: 'white' }
      },
      y: {
        ticks: { color: 'white', font: { weight: 'bold' }, stepSize: 1, precision: 0 },
        grid: { color: 'white' },
        beginAtZero: true
      }
    }
  }
});
</script>
<script>
function scrollToTop() {
  const wrapper = document.getElementById('resultWrapper');
  if (wrapper) {
    wrapper.scrollTo({top: 0, behavior: 'smooth'});
  } else {
    window.scrollTo({top: 0, behavior: 'smooth'});
  }
}
</script>
</body>
</html>
