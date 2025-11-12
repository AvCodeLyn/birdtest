<?php
$map = ['D' => 'orzel', 'I' => 'papuga', 'S' => 'golab', 'C' => 'sowa'];
$count = ['orzel' => 0, 'papuga' => 0, 'golab' => 0, 'sowa' => 0];
foreach ($_POST as $val) {
    $ptak = $map[$val];
    $count[$ptak]++;
}
$max = max($count);
$dominant = array_keys($count, $max);
$dominant_text = implode(', ', array_map('ucfirst', $dominant));

$ip = $_SERVER['REMOTE_ADDR'];
$conn = new mysqli("localhost", "user", "pass", "quiz_db");
$stmt = $conn->prepare("INSERT INTO quiz_results (ip_address, orzel, papuga, golab, sowa, dominant_birds) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siiiis", $ip, $count['orzel'], $count['papuga'], $count['golab'], $count['sowa'], $dominant_text);
$stmt->execute();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Twój wynik</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h2>Twój ptak: <?php echo $dominant_text; ?></h2>
  <canvas id="chart" width="400" height="200"></canvas>
  <script>
    const ctx = document.getElementById('chart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Orzeł', 'Papuga', 'Gołąb', 'Sowa'],
        datasets: [{
          label: 'Twój wynik',
          data: [<?php echo $count['orzel'] ?>, <?php echo $count['papuga'] ?>, <?php echo $count['golab'] ?>, <?php echo $count['sowa'] ?>],
          backgroundColor: ['#ff9999', '#ffe199', '#b0e57c', '#aecfff']
        }]
      }
    });
  </script>
</body>
</html>
