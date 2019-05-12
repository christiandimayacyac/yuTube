<?php 
    require_once APPROOT . '/views/inc/header.php';
    require_once APPROOT . '/classes/VideoPlayer.php';
    require_once APPROOT . '/classes/VideoInfoSection.php';
?>
<!-- Load Script to handle watch page actions -->
<script src="<?PHP echo URLROOT ;?>/js/watchActions.js"></script>

<div class="column-left">
    <div class="player-container">
        <?php
            $player = new VideoPlayer($data['currentVideo']);
            echo $player->create(false);
        ?>
    </div>
    <div class="video-header">
        <?php
            $player = new VideoInfoSection($data['loggedInUserId'], $data['currentVideo'], $data['currentVideoUploader'], $data['isSubscriber']);
            echo $player->create();
            
        ?>
    </div>
</div>

<div class="column-right">
    list
</div>

<?php 
    require APPROOT . '/views/inc/footer.php';
?>