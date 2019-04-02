<?php
    class  User {
        private $db;

        public function __construct() {
            $this->db = new Database;
        }

        public function register($data) {
            $this->db->query("INSERT INTO users (username, email, firstName, lastName, password, profilePicPath) VALUES (:username, :email, :firstName, :lastName, :password, :profilePicPath)");
            $this->db->bind(":username", $data['inputUserName']);
            $this->db->bind(":email", $data['inputEmail']);
            $this->db->bind(":firstName", $data['inputFirstName']);
            $this->db->bind(":lastName", $data['inputLastName']);
            //Encrypt the password before saving to the database
            // $hashed_password = password_hash($data['username'], PASSWORD_DEFAULT);
            $this->db->bind(":password", $data['inputPassword']);
            $this->db->bind(":profilePicPath", $data['profilePicPath']);
            $this->db->execute();
            
            //Check if inserting record successful
            if ( $this->db->getRowCount() == 1 ) {
                return true;
            }
            return false;
        }

        public function isUsernameExists($username) {
            $this->db->query("SELECT * FROM users WHERE username = :username");
            $this->db->bind(":username", $username);

            return ( $this->db->getResultRow() ) ? true : false;
        }

        public function getUserByEmail($email) {
            $this->db->query("SELECT * FROM users WHERE email = :email");
            $this->db->bind(":email", $email);
            
            if ( $userData = $this->db->getResultRow() ) {
                return $userData;
            }
            else {
                return false;
            }
        }

        public function getUserByUsername($username) {
            $this->db->query("SELECT * FROM users WHERE username = :username");
            $this->db->bind(":username", $username);
            
            return $this->db->getResultRow();
        }

        public function getUserById($id) {
            $this->db->query("SELECT * FROM users WHERE userId = :id");
            $this->db->bind(":id", $id);
            
            return $this->db->getResultRow();
        }

    }

?>