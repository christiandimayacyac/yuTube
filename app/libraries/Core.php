<?php
    /*
        App Core Class
        Creates URL & Loads core controller
        URL FORMAT - /controller/method/params

    */

    class Core {
        protected $currentController = 'Pages'; //variable to hold a Controller Class
        protected $currentMethod = 'index';
        protected $params = [];

        public function __construct() {
            // Retrieve the $_GET url as an array
            $url = $this->getUrl();
            //Check if file controller exists based on the URL from the controllers directory
            if ( file_exists('../app/controllers/' . ucwords($url[0]) . '.php') ) {
                // var_dump($url);
                //If controller exists, update the currentController   
                $this->currentController = ucwords($url[0]);   
                //unset $url[0] - Controller Name
                unset($url[0]);
            }
            // else {
            //     die("Invalid Controller!");
            // }

            //Require the controller file
            require_once '../app/controllers/' . $this->currentController . '.php';

            //Instantiate a New Controller Class
            $this->currentController = new $this->currentController;

            //Check for the second parameter/part of the URL (i.e. the method name)
            if ( isset($url[1]) ) {
                //Check if the method exists in the controller class
                if ( method_exists($this->currentController, $url[1]) ) {
                    $this->currentMethod = $url[1];
                    //unset the $url[1] - Method Name
                    unset($url[1]);
                }
            }

            //Retrieve the remaining parameters/parts of the URL
            $this->params = $url ? array_values($url) : [];

            //Call a callback method with parameters
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }

        public function getUrl() {
            if ( isset($_GET['url']) ) {
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);
                return $url;
            }
        }
    }