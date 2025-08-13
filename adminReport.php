<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reports</title>
  <link rel="stylesheet" href="css/adminReport.css" />
</head>
<body>
  <div class="report-container">
    <h1 class="report-title">Reports</h1>

    <!-- Top Summary Cards -->
    <div class="summary-cards">
      <div class="card">
        <img src="images/plasticBottle.png" alt="Plastic Bottles">
        <p>Total Contributions</p>
        <h2><?= htmlspecialchars($totalPlastic) ?></h2>
      </div>
      <div class="card">
        <img src="images/tincan.png" alt="Tin Cans">
        <p>Total Contributions</p>
        <h2><?= htmlspecialchars($totalCans) ?></h2>
      </div>
      <div class="card">
        <img src="images/glassBottle.png" alt="Glass Bottles">
        <p>Total Contributions</p>
        <h2><?= htmlspecialchars($totalGlassBottles) ?></h2>
      </div>
    <div class="date-picker">
    <span>📅 Filter by date:</span>
    <input type="date" id="date-filter" value="<?= date('Y-m-d') ?>">
    <button id="apply-filter">Apply</button>
    </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-reports">

      <!-- Graph Section -->
      <div class="contribution-graph">
        <h3>Contributions this month</h3>
        <div class="total-waste"><?= htmlspecialchars($totalWasteContributions) ?> Waste</div>
        <canvas id="contributionChart"></canvas>
      </div>

      <!-- Leading Zones (Placeholder Static for Now) -->
        <div class="leading-zones">
    <h3>Leading Zones</h3>
    <ul>
        <?php foreach ($contPerZone as $zone) { ?>
        <li><strong>Zone <?= $zone['zone'] ?></strong><span><?= $zone['totalContributions'] ?> total contributions</span></li>
        <?php } ?>
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
                // Filter total contributed items from $wasteHistory
                $userTotal = [
                  'Plastic' => 0,
                  'Bottles' => 0,
                  'Cans' => 0
                ];
                $totalPoints = 0;
                foreach ($wasteHistory as $entry) {
                  if ($entry['fullName'] === $user['fullName']) {
                    $material = $entry['materialName'];
                    $userTotal[$material] = ($userTotal[$material] ?? 0) + $entry['quantity'];
                    $totalPoints += $entry['pointsEarned'];
                  }
                }
              ?>
              <tr>
                <td><?= htmlspecialchars($user['fullName']) ?></td>
                <td><?= htmlspecialchars($user['zone']) ?></td>
                <td>
                  <?php foreach ($userTotal as $type => $count): ?>
                    <?= htmlspecialchars($type) ?> <?= $count ?><br/>
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

  <script src="[https://cdn.jsdelivr.net/npm/chart.js"></script>](https://cdn.jsdelivr.net/npm/chart.js"></script>)
  <script>
    document.getElementById('apply-filter').addEventListener('click', function() {
      var dateFilter = document.getElementById('date-filter').value;
      $.ajax({
        type: 'POST',
        url: 'php/chartdata.php',
        data: {date: dateFilter},
        success: function(data) {
          const ctx = document.getElementById('contributionChart').getContext('2d');
          const startDate = new Date(Date.parse(dateFilter));
          const endDate = new Date(startDate.getTime() + 28 * 24 * 60 * 60 * 1000);
        const ctx = document.getElementById('contributionChart').getContext('2d');
        const chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
              label: 'Waste',
              data: data, // Update the data with the response from the server
              borderColor: '#333',
              backgroundColor: '#fbb',
              fill: false,
              tension: 0.3,
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false }
            }
          }
        });
      }
    });
  });
</script>
</body>
</html>
