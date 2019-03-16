<?php 
    require APPROOT . '/classes/SignInFormProvider.php';
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

        <div class="login login-container">
            <div class="column">
                <div class="header">
                    <img src="<?php echo URLROOT; ?>/images/yutube.png" alt="yutube logo">
                    <h3>Sign In</h3>
                    <p>and experience the new yuTube</p>
                </div>
                <div class="login-form">
                    <?php 
                        $signInForm = new SignInFormProvider();
                        echo $signInForm->createSignInForm();
                    ?>
                </div>
                <div class="footer">
                    <p class="login-footer">Don't have an account yet? <a href="<?php echo URLROOT; ?>/users/register" class="login-register-link">Sign Up</a> here.</p>
                </div>
            </div>
        </div>
        
        <script src="<?php echo URLROOT ?>/js/jquery.js"></script>
        <script src="<?php echo URLROOT ?>/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo URLROOT ?>/js/main.js"></script>
    </body>
</html>