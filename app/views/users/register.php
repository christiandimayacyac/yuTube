<?php 
    require_once APPROOT . '/classes/RegisterFormProvider.php';
    require_once APPROOT . '/classes/FormSanitizer.php';
    require_once APPROOT . '/classes/AccountService.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <title><?php echo SITENAME ?></title>
</head>
    <body>
        
        <div class="signup signup-container">
            <div class="column">
                <div class="header">
                    <img src="<?php echo URLROOT; ?>/images/yutube.png" alt="yutube logo">
                    <h3>Sign Up</h3>
                    <p>and get a chance to create your channel</p>
                </div>
                <div class="signup-form">
                    <?php 
                        $registerForm = new RegisterFormProvider($data);
                        echo $registerForm->createRegisterForm();
                    ?>
                </div>
                <div class="footer">
                    <p class="signup-footer">Already have an account? <a href="<?php echo URLROOT; ?>/users/login" class="register-login-link">Sign In</a> here!</p>
                </div>
            </div>
        </div>
        
        <script src="<?php echo URLROOT ?>/js/jquery.js"></script>
        <script src="<?php echo URLROOT ?>/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo URLROOT ?>/js/main.js"></script>
    </body>
</html>