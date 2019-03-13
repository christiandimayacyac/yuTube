<?php
    /*
        Base Controller
        Loads Models and Views
    */

    class Controller {
        //Load the Model Class specified on the parameter
        public function loadModel($model) {
            //Require the model
            require_once '../app/models/' . $model . '.php';
            //Instantiate and return the model
            return new $model;
        }

        //Load the View Class specified on the parameter
        //Accept extra parameter as an array type for view usage
        public function loadView($view, $data = []) {
            //Check if the view exists
            if ( file_exists('../app/views/pages/' . $view . '.php') ) {
                //Require the view
                require_once '../app/views/pages/' . $view . '.php';
            }
            else {
                //Halt the web app
                die('View does not exists!');
            }
           
        }
    }

?>