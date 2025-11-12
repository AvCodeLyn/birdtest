<?php
if (!isset($_SESSION['quiz_access'])) {
    header('Location: index.php');
    exit;
}
include 'db.php';
$pytania = [  ['D' => 'Pewny siebie, Samodzielny, Zorientowany na cel, Ceni autonomię',
   'C' => 'Zdyscyplinowany, Systematyczny, Sprawny, Opanowany',
   'S' => 'Odpowiedzialny, Godny zaufania, Można na nim polegać, Punktualny',
   'I' => 'Ekspresyjny, Okazujący uczucia, Wyrażający się otwarcie, Gestykulujący'],

  ['I' => ' Spontaniczny, Działa bez ścisłego planu, Nieuporządkowany, Chaotyczny',
   'S' => 'Nieśmiały, Wrażliwy, Spokojny, Nie lubi być w centrum uwagi',
   'C' => 'Trzyma się ustalonych zasad, Nieugięty, Uporządkowany, Czepia się drobiazgów',
   'D' => 'Dominujący, Przejmujący inicjatywę, Stanowczy, Nierefleksyjny'],

  ['C' => ' Skrupulatny, Systematyczny, Dba o porządek, Zorganizowany',
   'S' => ' Opiekuńczy, Pomocny, Dobry słuchacz, Wyrozumiały',
   'I' => 'Kreatywny, Oryginalny  Ma dobrą intuicję, Pomysłowy',
   'D' => 'Produktywny, Ambitny, Dążący do celu, Skuteczny'],

  ['S' => 'Uparty, Nie rezygnuje, Nonkonformista, Odporny na wpływ',
   'C' => 'Perfekcyjny, Dąży do doskonałości, Dba o szczegóły, Pedantyczny',
   'D' => 'Kieruje się logiką, Lekceważy emocje innych, Opanowany, Dumny',
   'I' => 'Impulsywny, Działający szybko, Energiczny, Bez hamulców'],

  ['S' => 'Ciepły, Serdeczny, Życzliwy, Łagodny',
   'I' => 'Entuzjastyczny,  Optymistyczny, Zaangażowany, Inspirujący',
   'D' => 'Dzielny, Zdeterminowany, Śmiały, Gotowy na wyzwania',
   'C' => 'Dokładny, Ostrożny, Precyzyjny, Dąży do perfekcji'],

  ['C' => 'Nietowarzyski, Niekontaktowy, Skryty, Zamknięty w sobie',
   'D' => 'Niewdzięczny, Niedbały o innych, Nieczuły, Nietaktowny',
   'I' => 'Egocentryczny, Zagadujący na śmierć, Ślepy na innych, Pochłonięty sobą',
   'S' => 'Bierny, Uległy, Nieasertywny, Nieagresywny'],

  ['I' => 'Rozmowny, Ekspresyjny, Towarzyski, Przyjazny',
   'D' => ' Asertywny, Konkretny, Podejmujący decyzje, Działający',
   'C' => 'Analityczny, Badający, Skrupulatny, Oceniający',
   'S' => 'Lojalny, Niezawodny, Wspierający, Stały'],

  ['D' => 'Komenderujący, Mocny, Arogancki, Dogmatyczny',
   'I' => 'Niespokojny, Niecierpliwy, Nakręcony, Nie potrafi się zrelaksować',
   'S' => 'Powolny, Spokojny, Działa we własnym tempie, Zwlekający',
   'C' => ' Zatroskany, Ostrożny, Pełen obaw, Zmartwiony'],

  ['D' => ' Konkretny, Pewny siebie, Zdecydowany, Mocno osadzony',
   'C' => 'Krytycznie myślący, Właściwie analizuję, Dokładny, Precyzyjny',
   'S' => 'Przyjazny, Sympatyczny, Miły, Pogodny',
   'I' => ' Rozrywkowy, Żywy, Zabawny, Pełen humoru'],
   
  ['I' => 'Niestały, Nieodpowiedzialny, Nie można na nim polegać, Niepunktualny',
   'S' => 'Zależny, Polega na innych, Niepewny siebie, Chwiejny',
   'C' => 'Surowy, Narzuca reguły, Niecierpliwy, Karzący',
   'D' => 'Nietaktowny, Nieokrzesany, Niedelikatny, Depczący po odciskach'],

  ['C' => 'Planujący, Bada za i przeciw, Precyzyjny, Rozważny',
   'S' => 'Godny zaufania, Można na nim polegać, Uczciwy, Lojalny',
   'I' => 'Otwarty, Szczery, Komunikatywny, Ekspresyjny',
   'D' => 'Śmiały, Odważny, Działający z rozmachem, Nieustraszony'],

  ['S' => 'Niezdecydowany, Zbierający dane, Niekonkretny, Wahający się',
   'C' => 'Ostrożny, Nieufny, Dokładny, Rozważny',
   'D' => 'Twardy, Logiczny, Nieugięty, Zdeterminowany',
   'I' => 'Niekonsekwentny, Nielogiczny, Pełen sprzeczności, Kreatywny'],

  ['S' => 'Tolerancyjny, Cierpliwy, Szanujący, Akceptujący',
   'I' => 'Wszechstronny, Elastyczny, Twórczy, Pomysłowy',
   'D' => 'Zdecydowany, Ambitny, Stanowczy, Nieustępliwy',
   'C' => 'Precyzyjny, Dokładny, Konkretny, Odpowiedzialny'],

  ['C' => 'Drażliwy, Szybko się obraża, Wrażliwy, Łatwo urazić',
   'D' => 'Dominujący, Nieelastyczny, Agresywny, Rywalizacyjny',
   'I' => 'Ekspresyjny, Gaduła, Mówi, co ma na myśli, Asertywny',
   'S' => 'Wycofany, Łatwy w kontakcie, Bierny, Niegroźny'],

  ['I' => 'Wspaniałomyślny, Niesamolubny, Potrafi dawać, Lubi się dzielić',
   'D' => 'Bezpośredni, Przywódczy, Zdecydowany, Silny',
   'C' => 'Spostrzegawczy, Otwarty na informacje, Obserwujący, Rozróżniający',
   'S' => 'Tolerancyjny, Cierpliwy, Łatwo się przystosowuje, Dostosowujący się'],
   
  ['D' => 'Kontrolujący, Manipulacyjny, Wymuszający, Dyrygujący',
   'I' => 'Nadgorliwy, Pochopny, Impulsywny, Niespokojny',
   'S' => 'Uczuciowy, Wrażliwy, Głęboko przeżywający, Delikatny',
   'C' => 'Nieufny, Podejrzliwy, Uważny, Nastawiony obronnie'],
]; ?>

<main class="container-fluid p-0">
  <div class="quiz-container">
    <form method="POST" action="index.php?page=result" id="quizForm">
      <?php foreach ($pytania as $i => $odpowiedzi): ?>
        <div class="question-card <?= $i === 0 ? 'active animate-in' : '' ?>" data-index="<?= $i ?>">
          <div class="d-grid gap-3">
            <?php foreach ($odpowiedzi as $litera => $tekst): ?>
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
