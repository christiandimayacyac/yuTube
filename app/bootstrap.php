<?php

    // Load config
    require_once 'config/config.php';
    // Load Constants
    require_once 'classes/Constants.php';
    // Load URL Helper
    require_once 'helpers/urlhelper.php';
    require_once 'helpers/session_helper.php';
    require_once 'helpers/utilities_helper.php';

    // Automatic Library Loader 
    spl_autoload_register(function($classname){
        require_once 'libraries/' . $classname . '.php';
    });
?>
