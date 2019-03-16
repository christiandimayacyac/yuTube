<?php 
    require APPROOT . '/views/inc/header.php';
    require APPROOT . '/classes/VideoDetailsFormProvider.php';

?>

<div class="upload column">
    <?php 
        if ( !empty($data["fileErrors"]) ) {
            echo "<h1>" . $data["fileErrors"] [0]."</h1>";
        }

        $uploadForm = new VideoDetailsFormProvider($data['categories']); 
        echo $uploadForm->createUploadForm();
    ?>
</div>

<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content spinner">
        <p>Please wait...</p>
        <img src="<?php echo URLROOT; ?>/images/icons/spinner.gif">
    </div>
  </div>
</div>

<?php 
    require APPROOT . '/views/inc/footer.php';
?>