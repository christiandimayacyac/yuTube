<?php
    session_start();

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

    //Checks if the user is logged in
    // function isLoggedIn() {
    //     if ( isset($_SESSION['user_id']) && isset($_SESSION['user_email']) && isset($_SESSION['user_name']) ) {
    //         return true;
    //     }
    //     else {
    //         return false;
    //     }
    // }

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

?>