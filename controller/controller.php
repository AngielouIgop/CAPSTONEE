<?php
class Controller
{
    public $model = null;

    function __construct()
    {
        require_once('model/model.php');
        $this->model = new Model();
    }

    public function getWeb()
    {
        $command = isset($_GET['command']) ? $_GET['command'] : 'home';

        switch ($command) {
            case 'home':
                include_once('view/home.php');
                break;
            case 'about':
                include_once('view/about.php');
                break;
            case 'register':
                include('view/register.php');
                break;
            case 'processRegister':
                $fullname = $_POST['fullname'] ?? '';
                $email = $_POST['email'] ?? '';
                $contactNumber = $_POST['contactNumber'] ?? '';
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirm = $_POST['confirm'] ?? '';
                $registerRole = strtolower($_POST['registerRole'] ?? 'user');
                $error = '';

                // Common validation
                if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($confirm) || empty($contactNumber)) {
                    $error = "Please fill out all the required fields.";
                } elseif ($password !== $confirm) {
                    $error = "Passwords do not match.";
                } elseif ($this->model->userExists($username)) {
                    $error = "Username already exists.";
                } elseif (!in_array($registerRole, ['user', 'admin'])) {
                    $error = "Invalid role selected.";
                } else {
                    // Role-specific validation and data collection
                    if ($registerRole === 'user') {
                        $zone = $_POST['zone'] ?? '';
                        if (empty($zone)) {
                            $error = "Please enter your zone.";
                        } else {
                            $success = $this->model->registerUser($fullname, $email, $zone, $contactNumber, $username, $password, $registerRole);
                        }
                    } else { // admin
                        $position = $_POST['position'] ?? '';
                        if (empty($position)) {
                            $error = "Please enter your position.";
                        } else {
                            // Pass position as the zone parameter for admin registration
                            $success = $this->model->registerUser($fullname, $email, $position, $contactNumber, $username, $password, $registerRole);
                        }
                    }

                    if (isset($success) && $success) {
                        header("Location: ?command=login");
                        exit();
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }

                if ($error) {
                    include('view/register.php');
                }
                break;

            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $username = $_POST['username'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $loginRole = strtolower($_POST['loginRole'] ?? '');

                    $error = '';

                    // Check if user exists
                    if ($this->model->userExists($username)) {
                        // If user exists, attempt login with the loginRole
                        $user = $this->model->loginUser($username, $password, $loginRole);
                        if ($user) {
                            // Check if the role matches
                            if ($user['role'] === $loginRole) {
                                $_SESSION['user'] = $user;
                                if ($loginRole === 'admin') {
                                    header('Location: ?command=adminDashboard');
                                } else {
                                    header('Location: ?command=dashboard');
                                }
                                exit;
                            } else {
                                $error = "Selected role doesn't match your account.";
                            }
                        } else {
                            $error = "Invalid username or password.";
                        }
                    } else {
                        $error = "User does not exist.";
                    }
                }
                include_once('view/login.php');
                break;

            case 'dashboard':
                include_once('view/dashboard.php');
                break;


            case 'userProfile':
                if (!isset($_SESSION['user'])) {
                    header('Location: ?command=Login');
                    exit();
                }
                $userID = $_SESSION['user']['userID'];
                $users = $this->model->getUserData($userID);
                $totalCurrentPoints = $this->model->getUserPoints($userID);
                $wasteHistory = $this->model->getWasteHistory($userID);
                $rewards = $this->model->getAllRewards();

                // Debug line - remove after testing
                error_log("User ID: " . $userID . ", Points: " . $totalCurrentPoints);

                include_once('view/userProfile.php');
                break;

            case 'claim':
                if (!isset($_SESSION['user'])) {
                    header('Location: ?command=Login');
                }
                $userID = $_SESSION['user']['userID'];
                $users = $this->model->getUserPoints($userID);
                $totalCurrentPoints = $this->model->getUserPoints($userID);
                include_once('view/claim.php');
                break;

            case 'userSettings':
                if (!isset($_SESSION['user'])) {
                    header('Location: ?command=login');
                    exit();
                }
                $userID = $_SESSION['user']['userID'];
                $users = $this->model->getUserData($userID);

                include_once('view/userSettings.php');
                break;

            case 'updateProfileSettings': {
                // Check if user is logged in (either as user or admin)
                if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
                    echo "<script>alert('You must be logged in.'); window.location.href='?command=login';</script>";
                    exit();
                }

                // Determine session type and role
                $sessionType = isset($_SESSION['user']) ? 'user' : 'admin';
                $userID = $_SESSION[$sessionType]['userID'];
                $role = $_SESSION[$sessionType]['role'];

                // Get current user data to preserve unchanged fields
                $currentUser = $this->model->getUserById($userID);

                // Collect form data and only update if provided (not empty)
                $fullName = !empty($_REQUEST['fullname']) ? $_REQUEST['fullname'] : $currentUser['fullName'];
                $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : $currentUser['email'];
                $contactNumber = !empty($_REQUEST['contactNumber']) ? $_REQUEST['contactNumber'] : $currentUser['contactNumber'];
                $username = !empty($_REQUEST['username']) ? $_REQUEST['username'] : $currentUser['username'];
                $password = $_REQUEST['password'] ?? '';
                $confirmPassword = $_REQUEST['confirmPassword'] ?? '';

                // Handle different fields based on role
                if ($role === 'user') {
                    $zone = !empty($_REQUEST['zone']) ? $_REQUEST['zone'] : $currentUser['zone'];
                    $position = $currentUser['position']; // Keep existing position for users
                    $redirectCommand = 'userSettings';
                } else {
                    $position = !empty($_REQUEST['position']) ? $_REQUEST['position'] : $currentUser['position'];
                    $zone = $currentUser['zone']; // Keep existing zone for admins
                    $redirectCommand = 'adminProfile';
                }

                // Validate passwords match only if password is being changed
                if ($password && $password !== $confirmPassword) {
                    echo "<script>alert('Passwords do not match!'); window.location.href='?command=" . $redirectCommand . "';</script>";
                    break;
                }

                // Handle profile picture update
                $profilePicturePath = $currentUser['profilePicture']; // Keep current path by default
                
                // Check if user wants to remove profile picture
                $removeProfilePicture = isset($_REQUEST['removeProfilePicture']) && $_REQUEST['removeProfilePicture'] === '1';
                
                if ($removeProfilePicture) {
                    if ($profilePicturePath && file_exists($profilePicturePath)) {
                        if (unlink($profilePicturePath)) { // deletes file using unlink method
                            // Optional: Log or notify the success of file deletion
                        } else {
                            echo "<script>alert('Failed to delete the image file.'); window.location.href='?command=" . $redirectCommand . "';</script>";
                            break;
                        }
                    }
                    $profilePicturePath = null; // Set to null to remove from database
                } elseif (!empty($_FILES["profilePicture"]["name"])) {
                    // Delete old file if it exists
                    if ($profilePicturePath && file_exists($profilePicturePath)) {
                        if (unlink($profilePicturePath)) { // deletes file using unlink method
                            // Optional: Log or notify the success of file deletion
                        } else {
                            echo "<script>alert('Failed to delete the image file.'); window.location.href='?command=" . $redirectCommand . "';</script>";
                            break;
                        }
                    }
                    // Save new file
                    $targetDir = "profilePic/";
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES["profilePicture"]["name"]);
                    $newProfilePicturePath = $targetDir . $fileName;
                    $imageFileType = strtolower(pathinfo($newProfilePicturePath, PATHINFO_EXTENSION));
                    $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);

                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                    if ($check === false) {
                        echo "<script>alert('File is not an image.'); window.location.href='?command=" . $redirectCommand . "';</script>";
                        break;
                    }
                    if (!in_array($imageFileType, $allowedTypes)) {
                        echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.'); window.location.href='?command=" . $redirectCommand . "';</script>";
                        break;
                    }
                    if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $newProfilePicturePath)) {
                        echo "<script>alert('Failed to upload image.'); window.location.href='?command=" . $redirectCommand . "';</script>";
                        break;
                    }
                    $profilePicturePath = $newProfilePicturePath;
                }

                // Hash password only if it's being changed
                $hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

                // Update user in the database
                $result = $this->model->updateProfileSettings(
                    $userID,
                    $fullName,
                    $zone,
                    $position,
                    $email,
                    $contactNumber,
                    $username,
                    $hashedPassword,
                    $profilePicturePath
                );

                // Update session info with new values
                $_SESSION[$sessionType]['username'] = $username;
                $_SESSION[$sessionType]['fullName'] = $fullName;

                // Show result and redirect
                echo "<script>alert('Profile updated successfully.'); window.location.href='?command=" . $redirectCommand . "';</script>";
                break;
            }

            case 'contribute':
                include_once('view/contribute.php');
                break;

            case 'adminDashboard':
                include_once('view/admindashboard.php');
                break;

            case 'manageUser':
                include_once('view/manageUser.php');
                break;

            case 'adminProfile':
                if(!isset($_SESSION['user'])){
                    header('Location: ?command=Login');
                    exit();
                }

                $userID = $_SESSION['user']['userID'];
                $admin = $this->model->getUserData($userID);
                
                include_once('view/adminProfile.php');
                break;
                
            case 'rewardInventory':
                include_once('view/rewardinventory.php');
                break;
            case 'adminReport':
                include_once('view/adminReport.php');
                break;

            case 'logout':
                session_start();
                session_unset();
                session_destroy();
                header("Location: ?command=login");
                exit();
                break;

        }
    }

}

?>