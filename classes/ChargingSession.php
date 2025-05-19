<?php
class ChargingSession {
    // DB connection and table name
    private $conn;
    private $table_name = "charging_sessions";
    
    // Session properties
    public $session_id;
    public $user_id;
    public $location_id;
    public $check_in_time;
    public $check_out_time;
    public $total_cost;
    
    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Check in a user to a charging station
    public function checkIn() {
        // Sanitize input
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->location_id = htmlspecialchars(strip_tags($this->location_id));
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                (user_id, location_id)
                VALUES (?, ?)";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bind_param("ii", $this->user_id, $this->location_id);
        
        // Execute query
        if($stmt->execute()) {
            $this->session_id = $this->conn->insert_id;
            return true;
        }
        
        return false;
    }
    
    // Check out a user from a charging station
    public function checkOut($session_id) {
        // Get check-in time and cost per hour
        $query = "SELECT cs.check_in_time, cl.cost_per_hour
                FROM " . $this->table_name . " cs
                JOIN charging_locations cl ON cs.location_id = cl.location_id
                WHERE cs.session_id = ?
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $check_in_time = strtotime($row['check_in_time']);
            $check_out_time = time();
            $duration_hours = ($check_out_time - $check_in_time) / 3600; // Convert seconds to hours
            $total_cost = $duration_hours * $row['cost_per_hour'];
            
            // Update record
            $query = "UPDATE " . $this->table_name . "
                    SET check_out_time = NOW(), total_cost = ?
                    WHERE session_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("di", $total_cost, $session_id);
            
            if($stmt->execute()) {
                $this->total_cost = $total_cost;
                $this->check_out_time = date('Y-m-d H:i:s', $check_out_time);
                return true;
            }
        }
        
        return false;
    }
    
    // Get active session for a user
    public function getActiveSessionForUser($user_id) {
        $query = "SELECT cs.session_id, cs.user_id, cs.location_id, cs.check_in_time,
                    cl.description, cl.cost_per_hour
                FROM " . $this->table_name . " cs
                JOIN charging_locations cl ON cs.location_id = cl.location_id
                WHERE cs.user_id = ? AND cs.check_out_time IS NULL
                ORDER BY cs.check_in_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    
    // Get past sessions for a user
    public function getPastSessionsForUser($user_id) {
        $query = "SELECT cs.session_id, cs.user_id, cs.location_id, cs.check_in_time,
                    cs.check_out_time, cs.total_cost, cl.description, cl.cost_per_hour
                FROM " . $this->table_name . " cs
                JOIN charging_locations cl ON cs.location_id = cl.location_id
                WHERE cs.user_id = ? AND cs.check_out_time IS NOT NULL
                ORDER BY cs.check_out_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    
    // Check if user is already checked in somewhere
    public function isUserCheckedIn($user_id) {
        $query = "SELECT session_id FROM " . $this->table_name . "
                WHERE user_id = ? AND check_out_time IS NULL
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    // Get session by ID
    public function getSessionById($session_id) {
        $query = "SELECT cs.session_id, cs.user_id, cs.location_id, cs.check_in_time,
                    cs.check_out_time, cs.total_cost, cl.description, cl.cost_per_hour
                FROM " . $this->table_name . " cs
                JOIN charging_locations cl ON cs.location_id = cl.location_id
                WHERE cs.session_id = ?
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
}
?> 