<!-- A class that will hold the file and file data -->
<!-- The instance will the be used as a parameter in calling a function to save the file -->
<?php 
    class VideoDataService {
        private $targetDir;
        private $videoData;
        private $ffmpegpath;
        private $ffprobepath;
        private $outputLog = array();

        public function __construct($videoData) {
            $this->videoData = $videoData;
            // $this->ffmpegpath = "ffmpeg/bin/ffmpeg"; //on MAC machine relative path is needed
            $this->ffmpegpath = realpath("ffmpeg/bin/ffmpeg.exe"); //on WINDOWS machine absolute path is needed with.exe extension
            $this->ffprobepath = realpath("ffmpeg/bin/ffprobe.exe");
        }

        public function getTargetDir() {
            return $this->targetDir;
        }

        public function createFilePath() {
            //create a local server file path and not full url path
            $this->targetDir = "uploads/videos/";
            $filePath = $this->targetDir . uniqid() . "xXx" .basename($this->videoData->getVideoFile()["name"]);
            $filePath = str_replace(" ", "", $filePath);

            return $filePath;
        }

        public function validateVideoFile($filePath) {
            $fileErrors = [];
            $limit = 50*MB;

            //Check for a given valid video file extensions
            $valid_file_extensions = ['avi','mp4', 'mov', 'wmv'];
            $file_extension = $this->videoData->getFileExtension();

            if ( !in_array($file_extension, $valid_file_extensions) ) {
                array_push($fileErrors, "Invalid video file extension: " . $file_extension);
            }
            else {
                // Check for possible file duplicate
                if (file_exists($filePath)) {
                    array_push($fileErrors, "File already exists.");
                }
    
                // Check file size
                $size = $this->videoData->getVideoFile()["size"];
                if ( $this->videoData->getVideoFile()["size"] > $limit ) {
                    array_push($fileErrors, "Sorry, your file is too large with " . $size . " greater than " . $limit);
                }
            }
    
            return $fileErrors;
        }

        public function convertVideoFileToMp4($tempFileFullPath,$outputMp4FileFullPath) {
            //Declare a command to convert video file using ffmpeg.exe
            $cmd = "$this->ffmpegpath -i $tempFileFullPath $outputMp4FileFullPath 2>&1";  //the "2>&1 outputs any error on the screen"
            //Declare an array that will hold possible errors

            //Execute the command
            exec($cmd, $this->outputLog, $returnCode); //if returnCode == 0 means OK otherwise it failed

            //Check for returnCode; Display any errors occured
            if ( $returnCode != 0 ) {
                //Command failed
                foreach($this->outputLog as $line) {
                    echo $line . "<br>";
                }
                
                return false;
            }

            return true;
        }

        public function saveVideoDetails($dbVideos, $outputMp4FileFullPath,$duration) {
            if ( !($dbVideos->insertVideoDetails($this->videoData, $outputMp4FileFullPath, $duration)) ) {
                return false;
            }
            // elseif ( !($this->generateThumbnails($outputMp4FileFullPath) > 0) ) {
            //     return false;
            // }
            return true;
        }

        public function deleteFile($tempFile) {
            if ( !unlink($tempFile) ) {
                return false;
            }
            else {
                return true;
            }
        }

        public function generateThumbnails($dbVideos, $filePath, $duration, $lastInsertedVideoId) {
            $thumbnailSize = "210x118";
            $numOfThumbnails = 3;
            $pathToThumbnails = "uploads/videos/thumbnails";

            $duration = $this->getVideoDuration($filePath);

            for ( $i=1; $i <= $numOfThumbnails; $i++ ) {
                 $thumbnailName = $lastInsertedVideoId . "_" . uniqid() . ".jpg";
                 $thumbFullPath = "$pathToThumbnails/$thumbnailName";
                 $selected = ($i==1) ? 1 : 0;
                 
                 // Set 3 points in the duration for thumbnail grabbing
                 $interval = ( $duration * .8) / $numOfThumbnails * $i; 

                ////Generate thumbnail from the given interval
                //Declare a command to convert video file using ffmpeg.exe
                $cmd = "$this->ffmpegpath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $thumbFullPath 2>&1";  //-ss screenshot at $interval
                // ffmpeg -i c:\test.avi -ss 00:00:03 -s 210x118 -vframes 1 c:\xampp\htdocs\yutube\public\uploads\videos\thumbnails\test.jpg
                //Declare an array that will hold possible errors

                //Execute the command
                exec($cmd, $this->outputLog, $returnCode); //if returnCode == 0 means OK otherwise it failed

                //Check for returnCode; Display any errors occured
                if ( $returnCode != 0 ) {
                    //Command failed
                    foreach($this->outputLog as $line) {
                        echo $line . "<br>";
                    }
                    
                    // return false;
                }

                //Save the thumbnails and details in the directory and database respectively
                $this->saveThumbnails($dbVideos, $lastInsertedVideoId, $thumbFullPath, $selected);


            }
            
            return true;
        }

        public function saveThumbnails($dbVideos, $videoId, $thumbFullPath, $selected) {
            $dbVideos->insertThumbnails($videoId, $thumbFullPath, $selected);
        }

        public function getVideoDuration($filePath) {
            return (int)shell_exec("$this->ffprobepath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
        }

        // public function formatDuration($duration, $videoId) {
        public function formatDuration($duration) {
            $duration = (int)$duration; //cast the duration to an integer type

            $hours = floor($duration / 3600);  //divide in 3600 secs
            $minutes = floor(($duration - ($hours*3600)) / 60);
            $seconds = floor($duration % 60);

            $hours = ( $hours < 1 ) ? "" : $hours . ":";
            $minutes = ( $minutes < 10 ) ? "0".$minutes.":" : $minutes . ":";
            $seconds = ( $seconds < 10 ) ? "0".$seconds.":" : $seconds;
            
            return $hours.$minutes.$seconds;
        }

        public function incrementLike($videoId, $userId) {
            
        }

    }

?>