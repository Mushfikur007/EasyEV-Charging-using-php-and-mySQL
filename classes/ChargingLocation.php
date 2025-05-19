<?php
class ChargingLocation {
    // DB connection and table name
    private $conn;
    private $table_name = "charging_locations";
    
    // Location properties
    public $location_id;
    public $description;
    public $num_stations;
    public $cost_per_hour;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new charging location
    public function create() {
        // Sanitize input
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->num_stations = htmlspecialchars(strip_tags($this->num_stations));
        $this->cost_per_hour = htmlspecialchars(strip_tags($this->cost_per_hour));
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    description = ?,
                    num_stations = ?,
                    cost_per_hour = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bind_param("sid", $this->description, $this->num_stations, $this->cost_per_hour);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update charging location
    public function update() {
        // Sanitize input
        $this->location_id = htmlspecialchars(strip_tags($this->location_id));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->num_stations = htmlspecialchars(strip_tags($this->num_stations));
        $this->cost_per_hour = htmlspecialchars(strip_tags($this->cost_per_hour));
        
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    description = ?,
                    num_stations = ?,
                    cost_per_hour = ?
                WHERE
                    location_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bind_param("sidi", $this->description, $this->num_stations, $this->cost_per_hour, $this->location_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all charging locations
    public function getAllLocations() {
        $query = "SELECT location_id, description, num_stations, cost_per_hour, created_at, updated_at
                FROM " . $this->table_name . "
                ORDER BY location_id ASC";
        
        $result = $this->conn->query($query);
        return $result;
    }
    
    // Get charging location by ID
    public function getLocationById($id) {
        $query = "SELECT location_id, description, num_stations, cost_per_hour, created_at, updated_at
                FROM " . $this->table_name . "
                WHERE location_id = ?
                LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
    
    // Get locations with available stations
    public function getAvailableLocations() {
        $query = "SELECT cl.location_id, cl.description, cl.num_stations, cl.cost_per_hour,
                    (cl.num_stations - IFNULL(COUNT(CASE WHEN cs.check_out_time IS NULL THEN 1 ELSE NULL END), 0)) AS available_stations
                FROM " . $this->table_name . " cl
                LEFT JOIN charging_sessions cs ON cl.location_id = cs.location_id AND cs.check_out_time IS NULL
                GROUP BY cl.location_id
                HAVING available_stations > 0
                ORDER BY cl.location_id ASC";
        
        $result = $this->conn->query($query);
        return $result;
    }
    
    // Get locations that are full (no available stations)
    public function getFullLocations() {
        $query = "SELECT cl.location_id, cl.description, cl.num_stations, cl.cost_per_hour,
                    (cl.num_stations - IFNULL(COUNT(CASE WHEN cs.check_out_time IS NULL THEN 1 ELSE NULL END), 0)) AS available_stations
                FROM " . $this->table_name . " cl
                LEFT JOIN charging_sessions cs ON cl.location_id = cs.location_id AND cs.check_out_time IS NULL
                GROUP BY cl.location_id
                HAVING available_stations <= 0
                ORDER BY cl.location_id ASC";
        
        $result = $this->conn->query($query);
        return $result;
    }
    
    // Search locations by ID or description
    public function searchLocations($search_term) {
        $search_term = "%{$search_term}%";
        
        $query = "SELECT cl.location_id, cl.description, cl.num_stations, cl.cost_per_hour,
                    (cl.num_stations - IFNULL((
                        SELECT COUNT(*) FROM charging_sessions
                        WHERE cl.location_id = charging_sessions.location_id
                        AND check_out_time IS NULL
                    ), 0)) AS available_stations
                FROM " . $this->table_name . " cl
                WHERE cl.location_id LIKE ? OR cl.description LIKE ?
                ORDER BY cl.location_id ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    
    // Check if a location has available stations
    public function hasAvailableStations($location_id) {
        $query = "SELECT 
                    (cl.num_stations - IFNULL(COUNT(CASE WHEN cs.check_out_time IS NULL THEN 1 ELSE NULL END), 0)) AS available_stations
                FROM " . $this->table_name . " cl
                LEFT JOIN charging_sessions cs ON cl.location_id = cs.location_id AND cs.check_out_time IS NULL
                WHERE cl.location_id = ?
                GROUP BY cl.location_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $location_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['available_stations'] > 0;
        }
        
        return false;
    }
}
?> 