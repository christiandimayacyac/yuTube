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

        public function isEncUIDValid($encUID) {
            $userId = getBase64DecodedValue(Constants::$session_key, $encUID);
            return $userId;
        }

        //Checks if currentUser is subscribed to another given usdr
        public function isSubscribedTo($subscribedTo, $encUID) {
            $userId = getBase64DecodedValue(Constants::$session_key, $encUID);
            $uploaderUserId = getBase64DecodedValue(Constants::$data_key, $subscribedTo);

            $this->db->query("SELECT * FROM subscribers WHERE subscribedTo = :subscribedTo AND subscriberId = :subscriber");
            $this->db->bind(":subscribedTo", $uploaderUserId);
            $this->db->bind(":subscriber", $userId);

            $this->db->execute();

            return $this->db->getRowCount();

        }

        //Get the number of subscribers of the current channel
        public function getNumberOfSubscribers($encUID) {
            $userId = getBase64DecodedValue(Constants::$data_key, $encUID);

            $this->db->query("SELECT * FROM users WHERE userId = :subscribedTo");
            $this->db->bind(":subscribedTo", $userId);
            
            // $this->db->execute();
            $rs= $this->db->getResultRow();
            $subs = $rs->subscribers;

            return $subs;
        }

        //Increment Subscriber number of the current channel
        public function incSubscriber($encChannelUID) {
            $channelUID = getBase64DecodedValue(Constants::$data_key, $encChannelUID);

            //Get the current subscriber of current channel
            $currentSubscribers = (int)$this->getNumberOfSubscribers($encChannelUID);
            $currentSubscribers = (int)$currentSubscribers + 1;

            $this->db->query("UPDATE users SET subscribers = :subscribers WHERE userId = :userID");
            $this->db->bind(":subscribers", $currentSubscribers);
            $this->db->bind(":userID", $channelUID);

            $this->db->execute();

            return $this->db->getRowCount();
        }


        //Decrement Subscriber number of the current channel
        public function decSubscriber($encChannelUID) {
            $channelUID = getBase64DecodedValue(Constants::$data_key, $encChannelUID);

            //Get the current subscriber of current channel
            $currentSubscribers = (int)$this->getNumberOfSubscribers($encChannelUID);
            $currentSubscribers = (int)$currentSubscribers - 1;

            $this->db->query("UPDATE users SET subscribers = :subscribers WHERE userId = :userID");
            $this->db->bind(":subscribers", $currentSubscribers);
            $this->db->bind(":userID", $channelUID);

            $this->db->execute();

            return $this->db->getRowCount();
        }

        //Subscribe To a given user channel
        public function subscribeTo($channelOwnerId, $subscriberId) {
            // return "sub";
            $UID1 = getBase64DecodedValue(Constants::$data_key, $channelOwnerId);
            $UID2 = getBase64DecodedValue(Constants::$session_key, $subscriberId);

            $this->db->query("INSERT INTO subscribers (subscribedTo, subscriberId) VALUES (:channelOwnerId, :subscriberId)");
            $this->db->bind(":channelOwnerId", $UID1);
            $this->db->bind(":subscriberId", $UID2);

            $this->db->execute();
            
            //Update the number of subscribers in the users table
            $this->incSubscriber($channelOwnerId);

            return $this->db->getRowCount();
            // return $UID1;
        }

        //Unsubscribe To a given user channel
        public function unsubscribeTo($channelOwnerId, $subscriber) {
            // return "un";

            $UID1 = getBase64DecodedValue(Constants::$data_key, $channelOwnerId);
            $UID2 = getBase64DecodedValue(Constants::$session_key, $subscriber);
            
            $this->db->query("DELETE FROM subscribers WHERE subscribedTo = :subscribedTo AND subscriberId = :subscriberId");
            $this->db->bind(":subscribedTo", $UID1);
            $this->db->bind(":subscriberId", $UID2);

            $this->db->execute();
            
            //Update the number of subscribers in the users table
            $this->decSubscriber($channelOwnerId);
            return $this->db->getRowCount();
            // return $UID1;
        }

    }

?>