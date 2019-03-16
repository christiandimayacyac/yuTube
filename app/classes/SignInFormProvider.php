<?php
    class SignInFormProvider {

        public function createSignInForm() {
            $inputUser = $this->createInputUser();
            $inputPassword = $this->createInputPassword();
            $inputSubmit = $this->createSubmitButton();

            return "<form action='login' method='POST' enctype='multipart/form-data'>
                        $inputUser
                        $inputPassword
                        $inputSubmit
                    </form>";
        }
        
        public function createInputUser() {
            return "<div class='form-group'>
                        <input type='text' class='form-control form-control-lg' id='inputUsername' name='inputUsername' placeholder='Username' required>
                    </div>";
        }

        public function createInputPassword() {
            return "<div class='form-group'>
                        <input type='password' class='form-control form-control-lg' id='inputPassword' name='inputPassword' placeholder='Password' required>
                    </div>";
        }

        private function createSubmitButton() {
            return "<button type='submit' class='btn btn-primary' name='inputSubmit' id='inputSubmit'>Sign In</button>";
        }
    }

?>