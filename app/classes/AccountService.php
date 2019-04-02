<?php
    class AccountService {
        private $db;

        public function __construct($db) {
            $this->db = $db;
        }


        /**This function accepts POST Data and validates them 
         * after form sanitazion.
         */
        public function validateLoginCredentials($keys, $post_data, $post_err) {
            require_once '../app/classes/Constants.php';

            //Check empty data
            foreach($keys as $key) {
                if ( empty($post_data[$key]) ) {
                    $post_err[Constants::${$key}["err_label"] ] = Constants::${$key}["label"] ." is required.";
                } 
            }

            //Check for minimum and maximum lengths
            foreach($keys as $key) {
                if ( !checkLength($post_data[$key], Constants::${$key}) ) { //passes the sanitize POST data and the constant value assigned for the specified POST data
                    $post_err[Constants::${$key}["err_label"] ] = Constants::${$key}["label"] ." must be " . Constants::${$key}["min"] . " - " . Constants::${$key}["max"] . " characters long.";
                }
            }

            return $post_err;
        }




        /**This function accepts POST Data and validates them 
         * after form sanitazion.
         */
        public function validateRegistration($keys, $post_data, $post_err) {
            require_once '../app/classes/Constants.php';
            //// Validate Post data ////
            
                //Check empty data
                foreach($keys as $key) {
                    if ( empty($post_data[$key]) ) {
                        $post_err[Constants::${$key}["err_label"] ] = Constants::${$key}["label"] ." is required.";
                    } 
                }
                
                //Check for non-alphanumeric except spaces and apostrophe characters
                if ( !isAlphaNumSpaceApos($post_data['inputFirstName']) ) {
                    $post_err['fname_err'] = "First name may contain letters, space and apostrophe only.";
                }
                if ( !isAlphaNumSpaceApos($post_data['inputLastName']) ) {
                    $post_err['lname_err'] = "Last name may contain letters, space apostrophe only.";
                }

                //Check for Password Validity
                if ( !isPasswordEntryValid($post_data['inputPassword']) ) {
                    $post_err['password1_err'] = "Password must be at least 8 characters of any combination of least one lowercase, uppercase, number, and symbol.";
                }

                //Check for minimum and maximum lengths
                foreach($keys as $key) {
                    if ( !checkLength($post_data[$key], Constants::${$key}) ) { //passes the sanitize POST data and the constant value assigned for the specified POST data
                        $post_err[Constants::${$key}["err_label"] ] = Constants::${$key}["label"] ." must be " . Constants::${$key}["min"] . " - " . Constants::${$key}["max"] . " characters long.";
                    }
                }
                
                //Check possible username and email duplicate
                if ( $this->db->isUsernameExists($post_data['inputUserName']) && !empty($post_err['uname_err']) ) {
                    $post_err['uname_err'] = "Username is already taken.";
                }

                if ( $this->db->getUserByEmail($post_data['inputEmail']) ) {
                    $post_err['email1_err'] = "Email is already taken.";
                }
                elseif( $post_data['inputEmail'] != $post_data['inputConfirmEmail'] ) {
                    $post_err['email2_err'] = "Email does not match.";
                }

                

                //Check Email
                if ( $post_data['inputPassword'] != $post_data['inputConfirmPassword'] ) {
                    $post_err['password2_err'] = "Passwords do not match.";
                }
                
                //// END DATA VALIDATION ////


                //Merge POST DATA and INITIALIZED POST ERRORS 
                $data = array_merge($post_data, $post_err);

                // Check for any Post Data Errors
                if ( isErrorFree($post_err) ) {
                    // Hash the password
                    $data['inputPassword'] = password_hash($data['inputPassword'], PASSWORD_DEFAULT);

                    //Add default profile picture path to the POST DATA array
                    $data["profilePicPath"] = PROFILE_PIC_BASE_PATH . "default.jpg";
            
                    // Insert New User to the Database
                    if ( $this->db->register($data) ) {
                        //SEND AN ACTIVATION LINK TO THE USERS EMAIL
                        //TODO: EMAIL ACTIVATION FUNCTIONALITY
                        return $post_err;
                    }
                    else {
                        // Something went wrong with saving
                        return $post_err;
                        die('Something went wrong... Unable to insert record to the database'); //TODO: Refactor to an improved error page
                    }
                    
                }
                else {
                    //Return errors for views usage
                    return $post_err;
                }
        }

        public function createProfilePicFilePath() {
            //Create a local server file path and not full url path
            $this->targetDir = "uploads/profilepics/";
            $filePath = $this->targetDir . uniqid() . "xXx" .basename($this->videoData->getVideoFile()["name"]);
            $filePath = str_replace(" ", "", $filePath);

            return $filePath;
        }
    
    }

?>