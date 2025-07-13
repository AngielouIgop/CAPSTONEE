<?php
class Model {
  public $db = null;
  function __construct() {
    try{
        $this->db = new mysqli('localhost', 'root', '', 'capstone');
    } catch (Exception $e) {
        exit ('The database connection could not be established.');
    }
  } 

  public function loginUser($username, $password, $role) {
    $query = "SELECT * FROM user WHERE username = ? AND role = ?";
    if ($stmt = $this->db->prepare($query)) {
        $stmt->bind_param('ss', $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return $user; 
            }
        }
    }
    return false; 
}

public function getUserById($userID){
    $query = "SELECT * FROM user WHERE userID = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

  public function userExists($username){
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

public function registerUser($fullname, $email, $zone, $contactNumber, $username, $password, $role = 'user') {
    // Validate role
    if (!in_array($role, ['user', 'admin'])) {
        return false;
    }

    if ($this->userExists($username)) {
        return false;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // For users, use zone field
    if ($role === 'user') {
        $query = "INSERT INTO user (fullname, email, zone, contactNumber, username, password, role) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $fullname, $email, $zone, $contactNumber, $username, $hashedPassword, $role);
    } 
    // For admins, use position field (zone parameter contains position value for admins)
    else {
        $query = "INSERT INTO user (fullname, email, position, contactNumber, username, password, role) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssssss", $fullname, $email, $zone, $contactNumber, $username, $hashedPassword, $role);
    }
    
    return $stmt->execute();
}

public function getAllUser() {
    $result = $this->db->query("SELECT * FROM user");
    $users = [];

    while ($row = $result->fetch_assoc()){
        $users[] = $row;
    }
    return $users;
}

public function getUserData($userID){
    $stmt = $this->db->prepare("SELECT fullName, username, password, email, contactNumber, zone, profilePicture FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
               
public function calcPoints($userID, $materialID, $quantity){
    $query = "SELECT pointsPerItem FROM materialType WHERE materialID = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $materialID);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pointsPerItem = $row['pointsPerItem'];
        $pointsEarned = $pointsPerItem * $quantity;
        
        // Update the totalCurrentPoints in user table by adding the new points
        $updateQuery = "UPDATE user SET totalCurrentPoints = COALESCE(totalCurrentPoints, 0) + ? WHERE userID = ?";
        $updateStmt = $this->db->prepare($updateQuery);
        if (!$updateStmt) {
            error_log("Prepare failed: " . $this->db->error);
            return 0;
        }
        
        if (!$updateStmt->bind_param("ii", $pointsEarned, $userID)) {
            error_log("Binding parameters failed: " . $updateStmt->error);
            return 0;
        }

        if ($updateStmt->execute()){
            error_log("Points updated successfully. UserID: $userID, Points Earned: $pointsEarned");
            return $pointsEarned;
        } else {
            error_log("Execute failed: " . $updateStmt->error);
            return 0;
        }
    }
    return 0;
}

public function getWasteHistory($userID) {
    $query = "SELECT w.entryID, w.dateDeposited, w.timeDeposited, w.quantity, w.pointsEarned,
                     mt.materialName, mt.pointsPerItem
              FROM wasteEntry w 
              JOIN materialType mt ON w.materialID = mt.materialID 
              WHERE w.userID = ? 
              ORDER BY w.dateDeposited DESC, w.timeDeposited DESC";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    return $history;
}

public function getUserPoints($userID){
    $query = "SELECT totalCurrentPoints FROM user WHERE userID = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0){
        $row = $result->fetch_assoc();
        return $row['totalCurrentPoints'] ?? 0;
    }
    return 0;
}

public function updateProfileSettings($userID, $fullName, $zone, $position, $email, $contactNumber, $username, $hashedPassword = null, $profilePicturePath = null) {
    $fields = "fullName=?, zone=?, position=?, email=?, contactNumber=?, username=?";
    $types = "ssssss";
    $params = [$fullName, $zone, $position, $email, $contactNumber, $username];

    if ($hashedPassword !== null) {
        $fields .= ", password=?";
        $types .= "s";
        $params[] = $hashedPassword;
    }
    if ($profilePicturePath !== null) {
        $fields .= ", profilePicture=?";
        $types .= "s";
        $params[] = $profilePicturePath;
    }

    $params[] = $userID;
    $types .= "i";

    $sql = "UPDATE user SET $fields WHERE userID=?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();

    return $result ? "Profile Updated" : $stmt->error;
}

public function getPicturePathById($userID) {
    $stmt = $this->db->prepare("SELECT profilePicture FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['profilePicture'] : null; // returns the file path or null
}

public function getAllRewards() {
    $query = "SELECT * FROM reward ORDER BY pointsRequired ASC";
    $result = $this->db->query($query);
    $rewards = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rewards[] = $row;
        }
    }
    
    return $rewards;
}

}
?>