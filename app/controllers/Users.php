<?php
    class Users extends Controller {
        private $userModel;

        public function __construct() {
            $this->userModel = $this->loadModel("user");
        }

        public function index() {
            redirectTo("users/login");
            exit();
        }

        public function login() {

            //Check for cookie, validate and login if exists
            $this->autoLogin();

            //Initialize Error Data Array- Reset Value
            $keys = ['uname_err', 'password_err'];
            $post_err = initData($keys);

            //Initialize array that will hold the flash messages
            $login_message = array();

            if ( isset($_POST['inputSubmit']) && ($_SERVER['REQUEST_METHOD'] == 'POST') ) {
                //Require_once Constants and FormSanitizer for POST DATA processing
                require_once '../app/classes/Account.php';
                require_once '../app/classes/FormSanitizer.php';

                $account = new Account($this->userModel);

                //Batch Sanitize POST Data
                // $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                
                //Process the Form data - TRIM ONLY
                $keys = ['inputUserName', 'inputLoginPassword'];
                $post_data = populateData($keys);

                //Sanitize username and password
                $post_data['inputUserName'] = FormSanitizer::formUserNameSanitizer($post_data['inputUserName']);
                $post_data['inputLoginPassword'] = FormSanitizer::formPasswordSanitizer($post_data['inputLoginPassword']);
                
                
                $validationErrors = $account->validateLoginCredentials($keys, $post_data, $post_err);

                //Check if no errors in the POST DATA
                if ( isErrorFree($validationErrors) ) {
                    
                    //Check if username exists in the database
                    if ( $user = $this->userModel->getUserByUsername($post_data["inputUserName"]) ) {
                        //Check if account is activated
                        $activated = (int)$user->activated;
                        if ( $activated != 0 ) {
                            //verify password
                            if ( password_verify($post_data["inputLoginPassword"], $user->password) ) {
                                //Create User Sessions
                                $this->createUserSessions($user);

                                //Set Fingerprint
                                setFingerprint();

                                //Check if Remember me is checked
                                $remember = ( isset($_POST['inputRememberMe']) ) ? "yes" : '';

                                if ( $remember == "yes" ) {
                                    $encryptedID = getBase64EncodedValue(Constants::$cookie_key, $user->userId);
            
                                    //set a cookie that will expire after 30days
                                    setcookie("rememberMeCookie", $encryptedID, time()+60*60*24*100,"/");
                                }
                                
                                //Redirect to upload page
                                redirectTo("pages/upload/$_SESSION[uid]"); 
                                exit();
                            }
                            else {
                                array_push($login_message, "Invalid username or password!");
                                flash("flash_message", $login_message, "alert alert-danger");
                            }
                        }
                        else {
                            array_push($login_message, "Your account is not yet activated. Please check your email to activation link.");
                            flash("flash_message", $login_message,"alert alert-danger");
                        }
                    }
                    else { //No Username found
                        array_push($login_message, "Invalid username or password!");
                        flash("flash_message", $login_message, "alert alert-danger");
                    }
                    redirectTo('users/login');
                    exit();
                }
                else {
                    
                    //Merge POST DATA and encountered POST ERRORS 
                    $data = array_merge($post_data, $validationErrors);
                    // Load register view with validation error(s)
                    
                    $this->loadView('users/login', $data);
                }

            }
            else {
                //Initialize POST DATA values to ''
                $keys = ['inputUserName', 'inputPassword'];
                $post_data = initData($keys);

                //Merge POST DATA and INITIALIZED POST ERRORS
                $data = array_merge($post_data, $post_err); 
                //Load view
                $this->loadView("users/login", $data);
            }
 
            

        } //end function

        public function register() {
            //Initialize Error Data Array- Reset Value
            $keys = ['fname_err', 'lname_err', 'uname_err','email1_err','email2_err', 'password1_err', 'password2_err'];
            $post_err = initData($keys);

            //Initialize array that will hold the flash messages
            $register_message = array();

            if ( isset($_POST['inputSubmit']) && ($_SERVER['REQUEST_METHOD'] == 'POST') ) {
                
                //Require_once Account and FormSanitizer for POST DATA processing
                require_once '../app/classes/Account.php';
                require_once '../app/classes/FormSanitizer.php';
                
                //Instantiate Account that will validate the POST DATA
                $account = new Account($this->userModel);

                //Batch Sanitize POST Data
                // $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                //Process the Form data - TRIM ONLY
                $keys = ['inputFirstName', 'inputLastName', 'inputUserName', 'inputEmail','inputConfirmEmail', 'inputPassword', 'inputConfirmPassword'];
                $post_data = populateData($keys);
   

                //Custom Sanitize POST DATA
                $post_data['inputFirstName'] = FormSanitizer::formNameSanitizer($post_data['inputFirstName']);
                $post_data['inputLastName'] = FormSanitizer::formNameSanitizer($post_data['inputLastName']);

                $post_data['inputUserName'] = FormSanitizer::formUserNameSanitizer($post_data['inputUserName']);

                $post_data['inputEmail'] = FormSanitizer::formEmailSanitizer($post_data['inputEmail']);
                $post_data['inputConfirmEmail'] = FormSanitizer::formEmailSanitizer($post_data['inputConfirmEmail']);

                $post_data['inputPassword'] = FormSanitizer::formPasswordSanitizer($post_data['inputPassword']);
                $post_data['inputConfirmPassword'] = FormSanitizer::formPasswordSanitizer($post_data['inputConfirmPassword']);

                

                $validationErrors = $account->validateRegistration($keys, $post_data, $post_err);
                
                if ( isErrorFree($validationErrors) ) {
                    array_push($register_message, "You have successfully registered an account. Please check your email to activate your account.");
                    flash("flash_message", $register_message,"alert alert-success");
                    redirectTo('users/login');
                    exit();
                }
                else {
                    //Merge POST DATA and INITIALIZED POST ERRORS
                    $data = array_merge($post_data, $validationErrors); 

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

        public function logout() {
            // remove all session variables
            session_unset($_SESSION['uid']);
            session_unset($_SESSION['username']);
            
            //destroy all cookies
            if ( isset($_COOKIE['rememberMeCookie']) ){
                unset($_COOKIE['rememberMeCookie']);
                setcookie('rememberMeCookie', null, -1, '/');
            }

            // destroy the session 
            session_destroy(); 
            session_regenerate_id(true);

            redirectTo("users/login");
            exit();
        }

        private function autoLogin() {
            //Create Database instance for isCookieValid function
            $db = new Database();
            //Check if yutube cookie is present
            if ( isset($_COOKIE["rememberMeCookie"]) ) {
                if  ( !($rs = isCookieValid($db)) ) {
                    redirectTo("users/logout");
                    exit();
                }
                else {
                    //Automatic Login for the user
                    $user = $this->userModel->getUserById($rs->userId);
                    $this->createUserSessions($user);

                    //Redirect to Upload page
                    redirectTo("pages/upload/$_SESSION[uid]"); 
                    exit();
                }
            }
        }

        private function createUserSessions($user) {
            //Create User Sessions
            $_SESSION['uid'] = getBase64EncodedValue(Constants::$session_key, $user->userId);
            $_SESSION['loggedInUser'] = $user->username;

            //Set Fingerprint
            setFingerprint();

        }

    }

?>