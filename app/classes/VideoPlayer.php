<?php
    class VideoPlayer {
        private $videoObj;

        public function __construct($videoObj) {
            $this->videoObj = $videoObj;
        }

        public function create($autoplayFlag=false) {
            //Check if autoplay is turned on
            $autoplay = ($autoplayFlag) ? "autoplay" : "";
            // die(getcwd());
            $filePath = URLROOT."/".$this->videoObj->getVideoFilePath();
            
            return "<video class='player' controls $autoplay>
                        <source src='$filePath' type='video/mp4'>
                        Your browser does not support the video tag.
                    </video>";
        }
    }

?>