<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reports</title>
  <link rel="stylesheet" href="css/adminReport.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="report-container">
    <h1 class="report-title">Reports</h1>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="card">
        <img src="images/plasticBottle.png" alt="Plastic Bottles">
        <p>Total Plastic</p>
        <h2><?= htmlspecialchars($totalPlastic) ?></h2>
      </div>
      <div class="card">
        <img src="images/tincan.png" alt="Tin Cans">
        <p>Total Cans</p>
        <h2><?= htmlspecialchars($totalCans) ?></h2>
      </div>
      <div class="card">
        <img src="images/glassBottle.png" alt="Glass Bottles">
        <p>Total Bottles</p>
        <h2><?= htmlspecialchars($totalBottles) ?></h2>
      </div>
      <div class="date-picker">
        <span>📅 Filter by date:</span>
        <input type="date" id="date-filter" value="<?= date('Y-m-d') ?>">
        <button id="apply-filter">Apply</button>
      </div>
    </div>

    <!-- Main Reports -->
    <div class="main-reports">
      <!-- Graph -->
      <div class="contribution-graph">
        <h3>Contributions per Material (This Month)</h3>
        <canvas id="contributionChart"></canvas>
      </div>

      <!-- Leading Zones -->
    <div class="leading-zones">
    <h3>Total Contributions per Zone</h3>
    <ul>
        <li><strong>Zone 1</strong> <span><?= htmlspecialchars($getContZone1) ?> total contributions</span></li>
        <li><strong>Zone 2</strong> <span><?= htmlspecialchars($getContZone2) ?> total contributions</span></li>
        <li><strong>Zone 3</strong> <span><?= htmlspecialchars($getContZone3) ?> total contributions</span></li>
        <li><strong>Zone 4</strong> <span><?= htmlspecialchars($getContZone4) ?> total contributions</span></li>
        <li><strong>Zone 5</strong> <span><?= htmlspecialchars($getContZone5) ?> total contributions</span></li>
        <li><strong>Zone 6</strong> <span><?= htmlspecialchars($getContZone6) ?> total contributions</span></li>
        <li><strong>Zone 7</strong> <span><?= htmlspecialchars($getContZone7) ?> total contributions</span></li>
    </ul>
</div>


      <!-- Top Contributors Table -->
      <div class="top-contributors">
        <h3>Top Contributed Waste and Contributor</h3>
        <span class="mini-date">📅 <?= date('F d, Y') ?></span>
        <table>
          <thead>
            <tr>
              <th>Fullname</th>
              <th>Zone</th>
              <th>Total Contributed</th>
              <th>Total Current Points</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <?php
              $nameMap = [
                'Plastic' => 'Plastic Bottles',
                'Plastic Bottles' => 'Plastic Bottles',
                'Glass' => 'Glass Bottles',
                'Glass Bottles' => 'Glass Bottles',
                'Cans' => 'Cans',
                'Tin Cans' => 'Cans'
              ];
              $userTotal = ['Plastic Bottles' => 0, 'Glass Bottles' => 0, 'Cans' => 0];
              $totalPoints = 0;
              foreach ($wasteHistory as $entry) {
                if ($entry['fullName'] === $user['fullName']) {
                  $materialKey = $nameMap[$entry['materialName']] ?? null;
                  if ($materialKey) {
                    $userTotal[$materialKey] += $entry['quantity'];
                  }
                  $totalPoints += $entry['pointsEarned'];
                }
              }
              ?>
              <tr>
                <td><?= htmlspecialchars($user['fullName']) ?></td>
                <td><?= htmlspecialchars($user['zone']) ?></td>
                <td>
                  <?php foreach ($userTotal as $type => $count): ?>
                    <?= htmlspecialchars($type) ?>: <?= $count ?><br />
                  <?php endforeach; ?>
                </td>
                <td><?= $totalPoints ?> pts</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Chart.js Script -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const wastePerMaterial = <?= json_encode($wastePerMaterial) ?>;
        const labels = wastePerMaterial.map(item => item.materialType);
        const dataValues = wastePerMaterial.map(item => item.totalQuantity);

        const ctx = document.getElementById('contributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantity',
                    data: dataValues,
                    backgroundColor: ['#4cafef', '#81c784', '#ffb74d'],
                    borderColor: ['#1e88e5', '#388e3c', '#f57c00'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    });
  </script>
</body>
</html>
