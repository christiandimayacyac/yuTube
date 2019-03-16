<?php
    class Pages extends Controller{
        private $videosModel;

        public function __construct() {
            //Load Models here
            $this->videosModel = $this->loadModel('video');
        }

        public function index() {
            //TODO: load random videos at initial state

            $data = [
                'title' => 'YuTube'
            ];

            $this->loadView('pages/index', $data);
        }
        

        public function search($term) {
            //query the videos based on term
            $videos = $this->videosModel->queryVideos($term);

            //pass the queried videos based on term
            $data = [
                'title' => 'YuTube',
                'videos' => $videos
            ];
            
            $this->loadView('ipages/ndex', $data);
            
        }

        public function upload() {
            //query all categories
            $categories = $this->videosModel->queryCategories();

            $data = [
                'title' => 'YuTube',
                'categories' => $categories
            ];

            $this->loadView('pages/upload', $data);
        }

        public function about() {
            $data = [
                'title' => 'About Me',
                'developer' => 'Christian Dimayacyac'
            ];
            $this->loadView('pages/about', $data);
        }

        public function process() {
            //process POST data
            //declare errors array
            $flash_messages = array();

            if (isset($_POST['inputSubmit'])) {
                //Include a class that will hold the video fila data temporarily 
                //The instance will then be used for saving the file and file details in the target directory and database respectively
                require_once APPROOT."/classes/VideoUploadData.php";
                require_once APPROOT."/classes/VideoProcessData.php";

                $fileData = new VideoUploadData(
                                    $_FILES["inputFile"],
                                    $_POST["inputTitle"],
                                    $_POST["inputDescription"],
                                    $_POST["inputPrivacy"],
                                    $_POST["inputCategory"],
                                    "IAN"
                                );


                $fileObj = new VideoProcessData($fileData);     
                //Create fileFullPath for the temporary file
                $tempFileFullPath = $fileObj->createFilePath(); 

                //Validate file
                $fileErrors = $fileObj->validateVideoFile($tempFileFullPath);
                
                if ( !empty($fileErrors) ) {
                    //Flash error message and redirect back to upload page
                    flash("file_upload_status",  $fileErrors[0], "alert alert-danger");
                    redirectTo('pages/upload');
                }
                else {  
                    //Proceed with uploading temporary file for conversion
                    $tempFile = $fileData->getVideoFile()["tmp_name"];
                    
                    if(move_uploaded_file($tempFile, $tempFileFullPath)) {
                        //Create the outputMp4FileFullPath
                        $outputMp4FileFullPath = $fileObj->getTargetDir() . uniqid() . ".mp4";
                        
                        if ( $fileObj->convertVideoFileToMp4($tempFileFullPath, $outputMp4FileFullPath) ) {
                            //Save video details to the database
                            //Get video duration
                            $duration = $fileObj->getVideoDuration($outputMp4FileFullPath);

                            //Format duration
                            $formatted_duration = $fileObj->formatDuration($duration);

                            if ( $fileObj->saveVideoDetails($this->videosModel, $outputMp4FileFullPath, $formatted_duration) ) {
                         
                                //Generate thumbnails and save thumbnail details in the database
                                $lastInsertedVideoId = $this->videosModel->getLastInsertId();
                                if ( !($fileObj->generateThumbnails($this->videosModel, $outputMp4FileFullPath, $duration, $lastInsertedVideoId))) {
                                    array_push($flash_messages,"Warning: Incomplete operation: Unable to complete thumbnail generation operations");
                                }

                                //Delete the temporary file
                                if ( !($fileObj->deleteFile($tempFileFullPath)) ) {
                                    array_push($flash_messages, "Warning: Incomplete operation: Unable to delete temporary file.");
                                }
                                else {
                                    array_push($flash_messages, "File has been uploaded successfully.");
                                    //store flash messages to sessions in the session_helper.php
                                    flash("file_upload_status", $flash_messages);
                                    redirectTo('pages/upload');
                                    exit();
                                }
                            }
                            else {
                                //Error in saving video details to the database
                                array_push($flash_messages, "Warning: Incomplete operation: Unable to update database.");
                            }
                        }
                        else {
                            array_push($flash_messages, "Error: Unable to convert to MP4 format.");
                        }
                    }
                    else {
                        array_push($flash_messages, "There was an error in uploading the file.");
                    }

                    //store flash messages to sessions in the session_helper.php
                    flash("file_upload_status", $flash_messages, "alert alert-danger");
                    redirectTo('pages/upload');
                }     
            }
            else {
                //Redirect user to pages/upload if No POST data
                redirectTo('pages/upload');
            }
        }
    }
?>