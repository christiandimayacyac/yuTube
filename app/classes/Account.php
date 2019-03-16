<?php
    class Account {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }

        /**This function accepts POST Data and validates them 
         * after form sanitazion.
         */
        public function validateRegistration($firstName, $lastName, $userName, $email1, $email2, $password1, $password2) {
            
        }
    
    }

?>