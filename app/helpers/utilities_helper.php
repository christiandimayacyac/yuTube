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

?>