<?php
    class Users extends Controller {
        private $userModel;

        public function __construct() {
            $this->userModel = $this->loadModel("user");
        }

        public function index() {
            $data = [
                'title' => 'User Login'
            ];

            $this->loadView("users/index", $data);
        }

        public function login() {
            $data = [
                'title' => 'User Login'
            ];

            $this->loadView("users/login", $data);

        }

        public function registerx() {
            $data = [
                'title' => 'User Register'
            ];

            $this->loadView("users/register", $data);
        }

        public function register() {

            //Initialize Error Data Array- Reset Value
            $keys = ['fname_err', 'lname_err', 'uname_err','email1_err','email2_err', 'password1_err', 'password2_err'];
            $post_err = initData($keys);

            if ( isset($_POST['inputSubmit']) && ($_SERVER['REQUEST_METHOD'] == 'POST') ) {
                //Require_once Constants and FormSanitizer for POST DATA processing
                require_once '../app/classes/Constants.php';
                require_once '../app/classes/FormSanitizer.php';

                //Batch Sanitize POST Data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                //Process the Form data - TRIM ONLY
                $keys = ['inputFirstName', 'inputLastName', 'inputUserName', 'inputEmail','inputConfirmEmail', 'inputPassword', 'inputConfirmPassword'];
                $post_data = populateData($keys);

                //Custom Sanitize POST DATA
                $post_data['inputFirstName'] = FormSanitizer::formStringSanitizer($post_data['inputFirstName']);
                $post_data['inputLastName'] = FormSanitizer::formStringSanitizer($post_data['inputLastName']);

                $post_data['inputUserName'] = FormSanitizer::formUserNameSanitizer($post_data['inputUserName']);

                $post_data['inputEmail'] = FormSanitizer::formEmailSanitizer($post_data['inputEmail']);
                $post_data['inputConfirmEmail'] = FormSanitizer::formEmailSanitizer($post_data['inputConfirmEmail']);

                $post_data['inputPassword'] = FormSanitizer::formPasswordSanitizer($post_data['inputPassword']);
                $post_data['inputConfirmPassword'] = FormSanitizer::formPasswordSanitizer($post_data['inputConfirmPassword']);

                //// Validate Post data ////

                //Check empty data
                foreach($keys as $key) {
                    if ( empty($post_data[$key]) ) {
                        // $err_key = "Constants::$" .$key;
                        $post_err[Constants::${$key}["err_label"] ] = Constants::${$key}["label"] ." is required.";
                    } 
                }
                
                //Check possible username and email duplicate
                if ( $this->userModel->isUsernameExists($post_data['inputUserName']) ) {
                    $post_err['uname_err'] = "Username is already taken.";
                }

                if ( $this->userModel->getUserByEmail($post_data['inputEmail']) ) {
                    $post_err['email1_err'] = "Email is already taken.";
                }

                //Check for minimum and maximum lengths
                foreach($keys as $key) {
                    if ( !checkLength($post_data[$key], Constants::${$key}) ) { //passes the sanitize POST data and the constant value assigned for the specified POST data
                        $post_err[Constants::${$key}["err_label"] ] = Constants::${$key}["label"] ." must be " . Constants::${$key}["min"] . " - " . Constants::${$key}["max"] . " characters long.";
                    }
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
                    // Insert New User to the Database
                    if ( $this->userModel->register($data) ) {
                        //SEND AN ACTIVATION LINK TO THE USERS EMAIL
                        //TODO: EMAIL ACTIVATION FUNCTIONALITY

                        flash('register-success','You have successfully registered an account. Please check your email to activate your account.');
                        redirectTo('users/login');
                    }
                    else {
                        // Something went wrong with saving
                        die('Something went wrong... Unable to insert record to the database'); //TODO: Refactor to an improved error page
                    }
                    
                }
                else {
                    //Merge POST DATA and INITIALIZED POST ERRORS
                    $data = array_merge($post_data, $post_err); 

                    // Load register view with error(s)
                    $this->loadView('users/register', $data);
                }

                
            }
            else {
                //Initialize POST DATA values to ''
                $keys = ['inputFirstName', 'inputLastName', 'inputUserName', 'inputEmail','inputConfirmEmail', 'inputPassword', 'inputConfirmPassword'];
                $post_data = initData($keys);

                //Merge POST DATA and INITIALIZED POST ERRORS
                $data = array_merge($post_data, $post_err); 

                // Load view ("users/register");
                $this->loadView('users/register', $data);
            }
        }

    }

?>