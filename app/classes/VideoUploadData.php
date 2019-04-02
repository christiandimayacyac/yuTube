<!-- A class that will hold the file and file data -->
<!-- The instance will the be used as a parameter in calling a function to save the file -->
<?php 
    class VideoUploadData {
        private $videoFile;
        private $videoTitle;
        private $videoDescription;
        private $videoPrivacyType;
        private $videoCategory;
        private $videoUploaderId;

        private $fileExtension;
        private $duration;

        public function __construct($videoFile, $videoTitle, $videoDescription, $videoPrivacyType, $videoCategory, $videoUploaderId) {
            $this->videoFile = $videoFile;
            $this->videoTitle = $videoTitle;
            $this->videoDescription = $videoDescription;
            $this->videoPrivacyType = $videoPrivacyType;
            $this->videoCategory = $videoCategory;
            $this->videoUploaderId = $videoUploaderId;
            $this->fileExtension = $this->getFileExtension();
        }

        public function getVideoFile() {
            return $this->videoFile;
        }

        public function getVideoTitle() {
            return $this->videoTitle;
        }

        public function getVideoDescription() {
            return $this->videoDescription;
        }

        public function getVideoPrivacyType() {
            return $this->videoPrivacyType;
        }

        public function getVideoCategory() {
            return $this->videoCategory;
        }
        
        public function getVideoUploaderId() {
            return $this->videoUploaderId;
        }

        public function getFileExtension() {
            $filename = $this->videoFile["name"];
            $filename_array = explode('.',$filename);
            return end($filename_array);
        }

        // public function setDuration($duration) {
        //     $this->duration = $duration;
        // }
    }

?>