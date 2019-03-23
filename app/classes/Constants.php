<?php
    class Constants {
        /**Defines the min and max of the
         * Post data inputs from the sign in 
         * and sign up form
         */
        // public static $firstName_def = ["label"=>"First Name", "min"=>2, "max"=>50];
        // public static $lastName_def = ["label"=>"Last Name", "min"=>2, "max"=>50];
        // public static $userName_def = ["label"=>"Username", "min"=>6, "max"=>15];
        // public static $email_def = ["label"=>"Email", "min"=>7, "max"=>50];
        // public static $password_def = ["label"=>"Password", "min"=>6, "max"=>15];

        public static $inputFirstName = ["label"=>"First Name", "min"=>2, "max"=>50, "err_label"=>"fname_err"];
        public static $inputLastName = ["label"=>"Last Name", "min"=>2, "max"=>50, "err_label"=>"lname_err"];
        public static $inputUserName = ["label"=>"Username", "min"=>4, "max"=>15, "err_label"=>"uname_err"];
        public static $inputEmail = ["label"=>"Email", "min"=>7, "max"=>50, "err_label"=>"email1_err"];
        public static $inputConfirmEmail = ["label"=>"Confirm Email", "min"=>7, "max"=>50, "err_label"=>"email2_err"];
        public static $inputPassword = ["label"=>"Password", "min"=>6, "max"=>15, "err_label"=>"password1_err"];
        public static $inputLoginPassword = ["label"=>"Password", "min"=>6, "max"=>15, "err_label"=>"password_err"];
        public static $inputConfirmPassword = ["label"=>"Confirm Password", "min"=>6, "max"=>15, "err_label"=>"password2_err"];


        //base64encode security keys
        public static $session_key = "s3c3r3t";
        public static $cookie_key = "r3m3mb3rM3";

        //Cookie time limit for remember me in minutes
        public static $timeLimit = 30;

    }

?>