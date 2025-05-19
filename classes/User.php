<?php
class User {
    // DB connection and table name
    private $conn;
    private $table_name = "users";
    
    // User properties
    public $id;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $user_type;
    public $created_at;
    
    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register user
    public function register() {
        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->user_type = htmlspecialchars(strip_tags($this->user_type));
        
        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = ?,
                    email = ?,
                    phone = ?,
                    password = ?,
                    user_type = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bind_param("sssss", $this->name, $this->email, $this->phone, $password_hash, $this->user_type);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Login user
    public function login() {
        // Sanitize input
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        
        // Query to check if email exists
        $query = "SELECT id, name, email, phone, password, user_type
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 1";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bind_param("s", $this->email);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verify password
            if(password_verify($this->password, $row['password'])) {
                // Set properties
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->phone = $row['phone'];
                $this->user_type = $row['user_type'];
                
                return true;
            }
        }
        
        return false;
    }
    
    // Get all users
    public function getAllUsers() {
        $query = "SELECT id, name, email, phone, user_type, created_at
                FROM " . $this->table_name . "
                ORDER BY id ASC";
        
        $result = $this->conn->query($query);
        return $result;
    }
    
    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT id, name, email, phone, user_type, created_at
                FROM " . $this->table_name . "
                WHERE id = ?
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
    
    // Get all users currently checked-in
    public function getUsersCheckedIn() {
        $query = "SELECT u.id, u.name, u.email, u.phone, u.user_type, 
                    cl.location_id, cl.description, cs.check_in_time
                FROM " . $this->table_name . " u
                JOIN charging_sessions cs ON u.id = cs.user_id
                JOIN charging_locations cl ON cs.location_id = cl.location_id
                WHERE cs.check_out_time IS NULL
                ORDER BY cs.check_in_time DESC";
        
        $result = $this->conn->query($query);
        return $result;
    }
}
?> 