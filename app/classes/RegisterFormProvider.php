<?php
    class RegisterFormProvider {
        private $data;

        public function __construct($data) {
                $this->data = $data;
        }

        public function createRegisterForm() {
            $inputFirstName = $this->createInputFirstName();
            $inputLastName = $this->createInputLastName();
            $inputUserName = $this->createInputUserName();
            $inputEmail = $this->createInputEmail();
            $inputConfirmEmail = $this->createInputConfirmEmail();
            $inputPassword = $this->createInputPassword();
            $inputConfirmPassword = $this->createInputConfirmPassword();
            $inputSubmitButton = $this->createSubmitButton();

            return "<form action='register' method='POST' enctype='multipart/form-data'>
                        $inputFirstName
                        $inputLastName
                        $inputUserName
                        $inputEmail
                        $inputConfirmEmail
                        $inputPassword
                        $inputConfirmPassword
                        $inputSubmitButton
                    </form>";
        }
        
        public function createInputFirstName() {
            return "<div class='form-group'>
                        <input type='text' class='form-control form-control-lg " . (!empty($this->data['fname_err']) ? "is-invalid" : '') . "' id='inputFirstName' name='inputFirstName' placeholder='First Name' autocomplete='off' value='" . (isset($this->data['inputFirstName']) ? $this->data['inputFirstName'] : '') . "' required>" .
                        (!empty($this->data['fname_err']) ? "<span>{$this->data['fname_err']}</span>" : '') . 
                    "</div>";
        }

        public function createInputLastName() {
            return "<div class='form-group'>
                        <input type='text' class='form-control form-control-lg " . (!empty($this->data['lname_err']) ? "is-invalid" : '') . "' id='inputLastName' name='inputLastName' placeholder='Last Name' autocomplete='off' value='" . (isset($this->data['inputLastName']) ? $this->data['inputLastName'] : '') . "' required>" .
                        (!empty($this->data['lname_err']) ? "<span>{$this->data['lname_err']}</span>" : '') . 
                    "</div>";
        }

        public function createInputUserName() {
            return "<div class='form-group'>
                        <input type='text' class='form-control form-control-lg " . (!empty($this->data['uname_err']) ? "is-invalid" : '') . "' id='inputUserName' name='inputUserName' placeholder='Username' autocomplete='off' value='" . (isset($this->data['inputUserName']) ? $this->data['inputUserName'] : '') . "' required>" .
                        (!empty($this->data['uname_err']) ? "<span>{$this->data['uname_err']}</span>" : '') . 
                    "</div>";
        }

        public function createInputEmail() {
            return "<div class='form-group'>
                        <input type='email' class='form-control form-control-lg " . (!empty($this->data['email1_err']) ? "is-invalid" : '') . "' id='inputEmail' name='inputEmail' placeholder='Email' autocomplete='off' value='" . (isset($this->data['inputEmail']) ? $this->data['inputEmail'] : '') . "' required>" .
                        (!empty($this->data['email1_err']) ? "<span>{$this->data['email1_err']}</span>" : '') . 
                    "</div>";
        }

        public function createInputConfirmEmail() {
            return "<div class='form-group'>
                        <input type='email' class='form-control form-control-lg " . (!empty($this->data['email2_err']) ? "is-invalid" : '') . "' id='inputConfirmEmail' name='inputConfirmEmail' placeholder='Confirm Email' autocomplete='off' value='{$this->data['inputConfirmEmail']}' required>" .
                        (!empty($this->data['email2_err']) ? "<span>{$this->data['email2_err']}</span>" : '') . 
                    "</div>";
        }


        public function createInputPassword() {
            return "<div class='form-group'>
                        <input type='password' class='form-control form-control-lg " . (!empty($this->data['password1_err']) ? "is-invalid" : '') . "' id='inputPassword' name='inputPassword' placeholder='Password' required>" .
                        (!empty($this->data['password1_err']) ? "<span>{$this->data['password1_err']}</span>" : '') . 
                    "</div>";
        }

        public function createInputConfirmPassword() {
            return "<div class='form-group'>
                        <input type='password' class='form-control form-control-lg " . (!empty($this->data['password2_err']) ? "is-invalid" : '') . "' id='inputConfirmPassword' name='inputConfirmPassword' placeholder='Confirm Password' required>" .
                        (!empty($this->data['password2_err']) ? "<span>{$this->data['password2_err']}</span>" : '') . 
                    "</div>";
        }

        private function createSubmitButton() {
            return "<button type='submit' class='btn btn-primary' name='inputSubmit' id='inputSubmit'>Submit</button>";
        }
    }

?>