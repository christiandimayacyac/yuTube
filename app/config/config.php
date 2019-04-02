<?php 
    ob_start();

    //Set Default Time Zone to be used in date/time functions
    date_default_timezone_set("Asia/Manila");

    //DB Params
    define('DB_HOST', 'localhost');
    define('DB_USER', 'ziemdiadmin');
    define('DB_PASS', 'ziemdiadmin');
    define('DB_NAME', 'dbyutube');

    //ROOT PATHS
    define('APPROOT', dirname(dirname(__FILE__)));
    define('URLROOT', 'http://localhost/yutube');

    //SITENAME
    define('SITENAME', 'YuTube');

    //APP VERSION
    define('APP_VERSION', '1.0.0');

    //FILE SIZES 
    define('KB', 1024);
    define('MB', 1048576);
    define('GB', 1073741824);
    define('TB', 1099511627776);

    //PROFILE PIC BASE PATH
    define('PROFILE_PIC_BASE_PATH', 'uploads/profilepics/');
?>