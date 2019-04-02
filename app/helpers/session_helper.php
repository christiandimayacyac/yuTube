<?php
    session_start();
    $myname ="ian";

    //Function to reset sessions for flash messages
    function reset_flashmsg_sessions($name, $messages, $class) {  
        if ( isset($_SESSION[$name]) ) {
            unset($_SESSION[$name]);
        }
        if ( isset($_SESSION[$name.'_class']) ) {
            unset($_SESSION[$name.'_class']);
        }
        
        if ( isset($_SESSION['messages']) ) {
            unset($_SESSION['messages']);
        }
    }

    // Flash Messaging Function
    function flash($name = '', $messages = '', $class = 'alert alert-success') {
        if ( !empty($messages) ) {

            // unset sessions before setting a new value
            reset_flashmsg_sessions($name, $messages, $class);

            //Create Session Variables
            $_SESSION[$name] = $name;
            $_SESSION['messages'] = $messages;
            $_SESSION[$name.'_class'] = $class;
        } 
        elseif( empty($messages) && isset($_SESSION['messages']) ) {
            //set the html code block
            $html = "";
            $numOfMessages = count($_SESSION['messages']);
            if  ( $numOfMessages > 1 ) {
                foreach($_SESSION['messages'] as $message) {
                    $html .= "<div class='" . $_SESSION[$name.'_class'] ."' id='flash-msg'>" . $message . "</div>";
                }
            }
            else {
                $html = "<div class='" . $_SESSION[$name.'_class'] ."' id='flash-msg'>" . $_SESSION['messages'][0] . "</div>";
            }
            echo $html;
            // unset sessions after flashing messages
            reset_flashmsg_sessions($name, $messages, $class);
        }
    }

    function setFingerprint(){
		$_SESSION['fingerprint'] = getBase64EncodedValue($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
        $_SESSION['last_active'] = time();

	}

    function checkFingerprint(){
		$time_limit = 60 * Constants::$timeLimit; //Idle time limit in minutes
		$isValidFingerprint = true;
		
		// $fingerprint = $_SESSION['fingerprint'];
		$current_fingerprint = getBase64EncodedValue($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
		
		
		
		
		if ( isset($_SESSION['fingerprint']) && ($current_fingerprint !=  $_SESSION['fingerprint'])){
            $isValidFingerprint = false;
            redirectTo("users/logout");
            exit();
		}
		elseif ( isset($_SESSION['fingerprint']) && isset($_SESSION['last_active']) ) {
			$session_time = time() - $_SESSION['last_active'];
			if ( $session_time > $time_limit ){
                $isValidFingerprint = false;
				redirectTo("users/logout");
                exit();
			}
			else{
				$_SESSION['last_active'] = time();
				// $isValidFingerprint = true;
			}
		}
		else{
			
			$isValidFingerprint = false;
			// signOut();
		}
	
		return $isValidFingerprint;
    }
    
    function preLogin() {
        require_once "../app/classes/UserData.php";
        //Check if user sessions are set
        if ( isset($_SESSION["loggedInUser"]) && isset($_SESSION["uid"]) ) {
            $userLoggedIn = (isset($_SESSION["loggedInUser"]) ? $_SESSION["loggedInUser"] : "");
            $userLoggedInUID = (isset($_SESSION["uid"]) ? $_SESSION["uid"] : "");

            $userObj = new UserData($db, $userLoggedInUID);
            
            //Logout if no matching user; Sessions are deleted
            if ( ($userObj->getDbErrorFlag()) && !empty($userObj->getDbErrorMessage()) ) {
                redirectTo("user/logout");
                exit();
            }
    
            //Checks user's client machine fingerprint and idle time
            checkFingerprint();
        }
        
        autologin();
        
    }

    function autologin($userModel) {
        if ( isset($_COOKIE["rememberMeCookie"]) ) {  
            $db = new Database(); //check if a stored _COOKIE DATA is valid
            if  ( !($rs = isCookieValid($db)) ) {
                redirectTo("users/logout");
                exit();
            }
            elseif( isset($_SESSION["loggedInUser"]) && isset($_SESSION["uid"]) ) {
                redirectTo("pages/upload/$_SESSION[uid]"); 
                exit();
            }
            else {
                //Automatic Login for the user
                $user = $userModel->getUserById($rs->userId);
                createUserSessions($user);

                //Redirect to Upload page
                redirectTo("pages/upload/$_SESSION[uid]"); 
                exit();
            }
        }
    }

    function createUserSessions($user) {
        //Create User Sessions
        $_SESSION['uid'] = getBase64EncodedValue(Constants::$session_key, $user->userId);
        $_SESSION['loggedInUser'] = $user->username;

        //Set Fingerprint
        setFingerprint();

    }

    function isCookieValid($db) {
        //Decode the cookie
        $idFromCookie= getBase64DecodedValue(Constants::$cookie_key, $_COOKIE["rememberMeCookie"]);
        //Find id in the Users table; Return true if found, otherwise return false
        $db->query("SELECT userId FROM users WHERE userId = :id");
        $db->bind(":id", $idFromCookie);
        
        if  ( $rs = $db->getResultRow() ) {
            return $rs;
        }
        else {
            return false;
        }
    }

?>