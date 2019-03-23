<?php
    class SignInFormProvider {

        public function createSignInForm() {
            $inputUser = $this->createInputUser();
            $inputLoginPassword = $this->createinputLoginPassword();
            $inputSubmit = $this->createSubmitButton();
            $inputRememberMe = $this->createCheckboxRememberMe();

            return "<form action='login' method='POST' enctype='multipart/form-data'>
                        $inputUser
                        $inputLoginPassword
                        $inputRememberMe
                        $inputSubmit
                    </form>";
        }
        
        public function createInputUser() {
            return "<div class='form-group'>
                        <input type='text' class='form-control form-control-lg' id='inputUserName' name='inputUserName' placeholder='Username' required>
                    </div>";
        }

        public function createinputLoginPassword() {
            return "<div class='form-group'>
                        <input type='password' class='form-control form-control-lg' id='inputLoginPassword' name='inputLoginPassword' placeholder='Password' required>
                    </div>";
        }

        public function createCheckboxRememberMe() {
            // return "<div class='form-group'>
            //             <label for='inputRememberMe'>Remember Me</label>
            //             <input type='checkbox' class='form-control form-control-lg' id='inputRememberMe' name='inputRememberMe' value='yes'>
            //         </div>";
            return "
                    <div class='form-group remember'>
                        <input type='checkbox' id='inputRememberMe' name='inputRememberMe'>
                        <label for='inputRememberMe'>Remember me</label>
                    </div>";
        }

        private function createSubmitButton() {
            return "<button type='submit' class='btn btn-primary' name='inputSubmit' id='inputSubmit'>Sign In</button>";
        }
    }

?>