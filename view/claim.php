<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="css/claim.css">
  <title>Claim</title>
</head>
<body>
<div class="rewards-header">
  <span>Available Rewards:</span>
  <span style="float:right;">CURRENT POINTS: <b><?php echo htmlspecialchars($totalCurrentPoints); ?> pts</b></span>
</div>
<div class="rewards-list">
  <?php
    $maxCards = 8;
    $rewardCount = 0;
    if (!empty($rewards)) {
      foreach ($rewards as $reward) {
        $rewardCount++;
  ?>
    <div class="reward-card">
      <img src="<?php echo !empty($reward['rewardImg']) ? 'data:image/jpeg;base64,' . base64_encode($reward['rewardImg']) : 'images/default-reward.png'; ?>" alt="<?php echo htmlspecialchars($reward['rewardName']); ?>">
      <div class="reward-name"><?php echo htmlspecialchars($reward['rewardName']); ?></div>
      <div class="reward-points"><?php echo htmlspecialchars($reward['pointsRequired']); ?> pts</div>
      <button class="claim-btn <?php echo ($totalCurrentPoints >= $reward['pointsRequired']) ? 'available' : 'insufficient'; ?>"
        <?php echo ($totalCurrentPoints >= $reward['pointsRequired']) ? '' : 'disabled'; ?>>
        <?php echo ($totalCurrentPoints >= $reward['pointsRequired']) ? 'Claim' : 'Insufficient points'; ?>
      </button>
    </div>
  <?php
      }
    }
    // Add "coming soon" placeholders
    for ($i = $rewardCount; $i < $maxCards; $i++) {
  ?>
    <div class="reward-card coming-soon">
      <img src="images/default-reward.png" alt="Coming Soon">
      <div class="reward-name">coming soon</div>
    </div>
  <?php } ?>
</div>
</body>
</html>