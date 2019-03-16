<?php
    //DATA PROCESSING=======================//

    //Populate an array with trimmed data
    function populateData($keys) {
        // Populate the Associative array wih post data
        $data = [];
        foreach($keys as $key) {
            $data[$key] = trim($_POST[$key]);
        }

        return $data;
    }

    //Initialize blank data array
    function initData($keys) {
        // Initialize all keys with blank value
        $data = [];
        foreach($keys as $key) {
            $data[$key] = '';
        }
        
        return $data;
    }

    function isErrorFree($errors_array) {
        foreach($errors_array as $error) {
            if ( !empty($error)) {
                return false;
            }
        }
        return true;
    }

    function checkLength($str, $range) {
        if ( strlen($str) >= $range["min"] && strlen($str) <= $range["max"] ) {
            return true;
        }
        return false;
    }

    function checkEmptyPOSTData($keys, $post_data) {
        if ( empty($post_data[$keys]) ) {
            
        }
    }

?>