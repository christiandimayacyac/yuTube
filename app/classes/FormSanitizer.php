<?php
    /** Static Class used in sanitizing form data
     * 
     */
    class FormSanitizer {
        
        public static function formStringSanitizer($inputString) {
            $inputString = strip_tags($inputString);
            $inputString = preg_replace('/\s/', '', $inputString);
            $inputString = strtolower($inputString);
            $inputString = ucfirst($inputString);

            return $inputString;
        }

        public static function formUserNameSanitizer($inputString) {
            $inputString = strip_tags($inputString);
            $inputString = $inputText = preg_replace('/\s/', '', $inputString);

            return $inputText;
        }

        public static function formEmailSanitizer($inputEmail) {
            $inputEmail = strip_tags($inputEmail);
            $inputEmail = preg_replace('/\s/', '', $inputEmail);
            $inputEmail = filter_var($inputEmail, FILTER_VALIDATE_EMAIL);
         
            return $inputEmail;
        }

        public static function formPasswordSanitizer($inputPassword) {
            $inputPassword = strip_tags($inputPassword);

            return $inputPassword;
        }
    }

?>