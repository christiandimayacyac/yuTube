<?php
    
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
<div id="page-container">
    <div id="master-header-container">
        <button class="btnShowHide"><img src="<?php echo URLROOT; ?>/images/icons/menu.png" alt="menu"></button>
        <a href="<?php echo URLROOT ?>" class="site-logo-link">
            <img class="site-logo" src="<?php echo URLROOT; ?>/images/yutube.png" alt="site logo">
        </a>
        <div class="search-box-container">
            <form class="search-form">
                <input type="text" name="term" class="input-box" placeholder="Search...">
                <img src="<?php echo URLROOT; ?>/images/icons/search.png" alt="search button" class="search-button">
            </form>
        </div>

        <div class="menu-right">
            <?php if( isset($userObj) ) : ?>
                <p class="welcome">Welcome <span class="username"><?php echo $userObj->getFirstName(); ?></span>!</p>
            <?php endif; ?>
            <a href="<?php echo URLROOT . '/pages/upload/' . (isset($_SESSION['uid'])?$_SESSION['uid']:'') ; ?>" class="btn-right link-upload">
                <img src="<?php echo URLROOT; ?>/images/icons/upload.png" alt="upload">
            </a>
            <a href="<?php echo URLROOT.'/pages/watch/'.(isset($_SESSION['uid'])?$_SESSION['uid']:'').'/112'; ?>" class="btn-right link-avatar">
                <img src="<?php echo URLROOT; ?>/uploads/profilepics/default.png" alt="avatar">
            </a>
        </div>
    </div>

    <div id="side-nav-container" style="display:none;">

    </div>

    <div id="main-section-container">
        <div id="main-content-container">