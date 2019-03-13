<?php

    // Load config
    require_once 'config/config.php';
    // Load URL Helper
    require_once 'helpers/urlhelper.php';
    require_once 'helpers/session_helper.php';

    // Automatic Library Loader 
    spl_autoload_register(function($classname){
        require_once 'libraries/' . $classname . '.php';
    });

?>
