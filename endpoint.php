<?php
session_start();
echo session_id();

class endpoint {
    // Include the model file
    public $model = null;

    function __construct() {
        require_once('model/model.php');
        $this->model = new Model();
    }

    public function processRequest() {
        //getting the userID
        if($_SERVER['REQUEST_METHOD'] ==='GET') {
            header('Content-Type: application/json');

            if(!isset($_SESSION['userID'])){
                http_response_code(401);
                echo json_encode(['error' => 'User not logged in']);
                exit;
            }

            echo json_encode([
                'userID'=> $_SESSION['userID'],
                'username' => $_SESSION['username'] ?? 'Unknown'
            ]);
            exit;
        }


        // Debug incoming request
        error_log("=== New Request ===");
        error_log("POST data: " . print_r($_POST, true));
        
        // Get POST data with null coalescing operator
        $material = $_POST['material'] ?? '';
        $sensor_value = $_POST['sensor_value'] ?? '';
        $dateDeposited = date('Y-m-d');
        $timeDeposited = date('H:i:s');
        $userID = $_POST['userID'] ?? '';

        // Debug received data
        error_log("Processed data:");
        error_log("Material: $material");
        error_log("Sensor Value: $sensor_value");
        error_log("UserID: $userID");
        error_log("Date: $dateDeposited");
        error_log("Time: $timeDeposited");

        // Validate input data
        if (empty($material) || empty($sensor_value) || empty($userID)) {
            error_log("Missing required data - Material: $material, Sensor Value: $sensor_value, UserID: $userID");
            die("Error: Missing required data");
        }

        try {
            // Check if userID exists in the user table
            $userCheck = $this->model->db->prepare("SELECT userID FROM user WHERE userID = ?");
            if (!$userCheck) {
                throw new Exception("Error preparing user check statement: " . $this->model->db->error);
            }
            
            $userCheck->bind_param("i", $userID);
            $userCheck->execute();
            $userCheckResult = $userCheck->get_result();
            error_log("User check result rows: " . $userCheckResult->num_rows);

            if ($userCheckResult && $userCheckResult->num_rows > 0) {
                // Get materialID and pointsPerItem based on the material type
                $materialQuery = "SELECT materialID, pointsPerItem FROM materialType WHERE materialName = ?";
                $materialStmt = $this->model->db->prepare($materialQuery);
                if (!$materialStmt) {
                    throw new Exception("Error preparing material statement: " . $this->model->db->error);
                }
                
                $materialStmt->bind_param("s", $material);
                $materialStmt->execute();
                $materialResult = $materialStmt->get_result();
                error_log("Material check result rows: " . $materialResult->num_rows);
                
                if ($materialResult && $materialResult->num_rows > 0) {
                    $materialRow = $materialResult->fetch_assoc();
                    $materialID = $materialRow['materialID'];
                    $quantity = 1;
                    error_log("Found materialID: $materialID");

                    // Calculate points earned
                    $pointsEarned = $this->model->calcPoints($userID, $materialID, $quantity);
                    error_log("Calculated points: $pointsEarned");

                    // Insert waste entry
                    $sql = "INSERT INTO wasteEntry (userID, materialID, quantity, pointsEarned, dateDeposited, timeDeposited)
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $this->model->db->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparing waste entry statement: " . $this->model->db->error);
                    }

                    $stmt->bind_param("iiisss", $userID, $materialID, $quantity, $pointsEarned, $dateDeposited, $timeDeposited);
                    error_log("Attempting to insert waste entry...");

                    if ($stmt->execute()) {
                        echo "Success: Material detected and points awarded";
                        error_log("Success: Material $material detected for user $userID, points awarded: $pointsEarned");
                    } else {
                        throw new Exception("Error inserting waste entry: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    throw new Exception("Material not found in database: $material");
                }
                $materialStmt->close();
            } else {
                throw new Exception("Invalid userID: $userID");
            }
            $userCheck->close();
            
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
        } finally {
            // No need to close $this->db here, as it's managed by the model
        }
    }
}

// Create instance and process request
$endpoint = new endpoint();
$endpoint->processRequest();
?>