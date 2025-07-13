<link rel="stylesheet" href="css/contribute.css">
<!-- Modal Overlay -->
<div id="contributeModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <img src="images/basura logo.png" alt="Basura Logo" class="modal-logo" />
      <span class="modal-title">B.A.S.U.R.A. Rewards</span>
    </div>
    <div class="modal-body">
      <p class="modal-instruction">Please insert your waste</p>
      <div class="progress-bar-bg">
        <div class="progress-bar-fill"></div>
      </div>
      <div class="modal-actions">
        <button class="modal-btn done-btn">Done</button>
        <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
    function closeModal() {
        document.getElementById('contributeModal').style.display = 'none';
    }
</script>