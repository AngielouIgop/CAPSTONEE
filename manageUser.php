<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/manageUser.css">
</head>

<body>
     <button class="pill-btn add-admin-btn" style="float:right;">Add an Admin</button>    
    <div class="section-header">
        Manage Users
        <!-- <button class="pill-btn" style="float:right;">Pending Registrations</button> -->
    </div>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Zone</th>
                    <th>Total Accumulated Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><span class="profile-icon">👤</span></td>
                        <td><?= htmlspecialchars($user['fullName']) ?></td>
                        <td><?= htmlspecialchars($user['contactNumber']) ?></td>
                        <td><?= htmlspecialchars($user['zone']) ?></td>
                        <td><?= htmlspecialchars($user['totalCurrentPoints']) ?> pts</td>
                        <td>
                            <a href="#" class="action-btn edit-btn" data-userid="<?= $user['userID']; ?>"
                                data-fullname="<?= htmlspecialchars($user['fullName']); ?>"
                                data-email="<?= htmlspecialchars($user['email']); ?>"
                                data-contactnumber="<?= htmlspecialchars($user['contactNumber']); ?>"
                                data-zone="<?= htmlspecialchars($user['zone']); ?>"
                                data-username="<?= htmlspecialchars($user['username']); ?>">Edit</a>
                            <a href="index.php?command=deleteUser&userID=<?= $user['userID']; ?>" class="action-btn"
                                onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($user['username'] ?? $user['contactNumber'] ?? 'this user'); ?>?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section-header">
        Admins
    </div>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Date Added</th>
                    <!-- Actions column removed -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><span class="profile-icon">👤</span></td>
                        <td><?= htmlspecialchars($admin['fullName']) ?></td>
                        <td><?= htmlspecialchars($admin['contactNumber']) ?></td>
                        <td><?= htmlspecialchars($admin['position']) ?></td>
                        <td><?= htmlspecialchars($admin['registrationDate']) ?></td>
                        <!-- Actions column removed -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>


<!-- Edit User Details Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit User Details</h2>
        </div>
        <form id="editUserForm" action="index.php?command=updateUserProfile" method="POST">
            <input type="hidden" id="edit-userID" name="userID">

            <label for="edit-fullname">Fullname</label>
            <input type="text" id="edit-fullname" name="fullname" required>

            <label for="edit-email">Email</label>
            <input type="email" id="edit-email" name="email" required>

            <label for="edit-zone">Zone</label>
            <input type="text" id="edit-zone" name="zone" required>

            <label for="edit-contactNumber">Contact Number</label>
            <input type="text" id="edit-contactNumber" name="contactNumber" required>

            <label for="edit-username">Username</label>
            <input type="text" id="edit-username" name="username" required>

            <label for="edit-password">Password</label>
            <div class="password-container">
                <input type="password" id="edit-password" name="password">
                <button type="button" class="password-toggle" onclick="togglePassword('edit-password', this)">Show</button>
            </div>
            <small>Leave blank to keep current password</small>

            <label for="edit-confirmPassword">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="edit-confirmPassword" name="confirmPassword">
                <button type="button" class="password-toggle" onclick="togglePassword('edit-confirmPassword', this)">Show</button>
            </div>

            <div class="modal-buttons">
                <button type="submit" class="btn-confirm">Confirm</button>
                <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add an Administrator Modal -->
<div id="addAdministratorModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add a New Administrator</h2>
        </div>
        <form id="addAdministratorForm" action="index.php?command=addAdministrator" method="POST">
            <input type="hidden" id="add-userID" name="userID">

            <label for="add-fullname">Fullname</label>
            <input type="text" id="add-fullname" name="fullname" required>

            <label for="add-email">Email</label>
            <input type="email" id="add-email" name="email" required>

            <label for="add-position">Position</label>
            <input type="text" id="add-position" name="position" required>

            <label for="add-contactNumber">Contact Number</label>
            <input type="text" id="add-contactNumber" name="contactNumber" required>

            <label for="add-username">Username</label>
            <input type="text" id="add-username" name="username" required>

            <label for="add-password">Password</label>
            <div class="password-container">
                <input type="password" id="add-password" name="password">
                <button type="button" class="password-toggle" onclick="togglePassword('add-password', this)">Show</button>
            </div>

            <label for="add-confirmPassword">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="add-confirmPassword" name="confirmPassword">
                <button type="button" class="password-toggle" onclick="togglePassword('add-confirmPassword', this)">Show</button>
            </div>

            <label for="add-profilePicture">Profile Picture</label>
            <input type="file" id="add-profilePicture" name="profilePicture" accept="image/*">

            <div class="modal-buttons">
                <button type="submit" class="btn-confirm">Confirm</button>
                <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(inputId, toggleBtn) {
    const passwordInput = document.getElementById(inputId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = 'Hide';
        toggleBtn.title = 'Hide password';
    } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = 'Show';
        toggleBtn.title = 'Show password';
    }
}
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editUserModal');
        const editBtns = document.querySelectorAll('.edit-btn');
        const cancelBtn = document.getElementById('cancelBtn');

        editBtns.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                // Populate form
                document.getElementById('edit-userID').value = this.dataset.userid;
                document.getElementById('edit-fullname').value = this.dataset.fullname;
                document.getElementById('edit-email').value = this.dataset.email;
                document.getElementById('edit-zone').value = this.dataset.zone;
                document.getElementById('edit-contactNumber').value = this.dataset.contactnumber;
                document.getElementById('edit-username').value = this.dataset.username;
                document.getElementById('edit-password').value = '';
                document.getElementById('edit-confirmPassword').value = '';

                modal.style.display = 'block';
            });
        });

        cancelBtn.addEventListener('click', function () {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addAdminModal = document.getElementById('addAdministratorModal');
        const addAdminBtn = document.querySelector('.add-admin-btn'); // or use '#addAdminBtn' if you used id
        const addCancelBtn = addAdminModal.querySelector('.btn-cancel');

        // Open modal on button click
        addAdminBtn.addEventListener('click', function (e) {
            e.preventDefault();
            addAdminModal.style.display = 'block';
        });

        // Close modal on cancel
        addCancelBtn.addEventListener('click', function () {
            addAdminModal.style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', function (event) {
            if (event.target == addAdminModal) {
                addAdminModal.style.display = 'none';
            }
        });
    });
</script>

</html>