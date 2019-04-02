<?php
    class UserData {
        private $db;
        private $id;
        private $userName;
        private $firstName;
        private $lastName;
        private $fullName;
        private $email;
        private $dateRegistered;
        private $activatedFlag;
        private $numOfSubscribers;
        private $dbErrorFlag;
        private $dbErrorMessage;

        public function __construct($db, $encUID) {
            $this->db =$db;
            $this->id = getBase64DecodedValue(Constants::$session_key ,$encUID);
            $this->getUserDetails();

            $this->dbErrorFlag = false;
            $this->dbErrorMessage = "";
        }

        private function getUserDetails() {
            try {
                $this->db->query("SELECT * FROM users WHERE userId = :id");
                $this->db->bind(":id",$this->id);
                $rs = $this->db->getResultRow();

                if ( !empty($rs) ) {
                    $this->userName = $rs->username;
                    $this->firstName = $rs->firstName;
                    $this->lastName = $rs->lastName;
                    $this->fullName = $rs->firstName . " " . $rs->lastName;
                    $this->email = $rs->email;
                    $this->numOfSubscribers = $rs->subscribers;
                    $this->dateRegistered = $rs->dateCreated;
                    $this->activatedFlag = $rs->activated;
                }
            }
            catch(PDOException $ex) {
                $this->dbErrorFlag = true;
                $this->dbErrorMessage = "Error in reading database.";
                exit();
            }
            
            $this->dbErrorFlag = false;
            $this->dbErrorMessage = "";
        }

        public function getUserId() {
            return $this->id;
        }

        public function getUserName() {
            return $this->userName;
        }

        public function getFirstName() {
            return $this->firstName;
        }

        public function getLastName() {
            return $this->lastName;
        }

        public function getFullName() {
            return $this->fullName;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getDateRegistered() {
            return $this->dateRegistered;
        }

        public function getActivationFlag() {
            return $this->activatedFlag;
        }
        
        public function getNumOfSubscribers() {
            return $this->numOfSubscribers;
        }
        
        public function getDbErrorFlag() {
            return $this->activatedFlag;
        }
        
        public function getDbErrorMessage() {
            return $this->dbErrorMessage;
        }
    }

?>