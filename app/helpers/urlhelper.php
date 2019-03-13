<?php
  // Simple page redirect
  function redirectTo($page){
    header('location: ' . URLROOT . '/' . $page);
  }

?>