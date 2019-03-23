<?php
    class Pages extends Controller{
        private $videoModel;
        private $userModel;
        private $userObj;

        public function __construct() {
            //Load Models here
            $this->videoModel = $this->loadModel('video');
            $this->userModel = $this->loadModel('user');

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
            $videos = $this->videoModel->queryVideos($term);

            //pass the queried videos based on term
            $data = [
                'title' => 'YuTube',
                'videos' => $videos
            ];
            
            $this->loadView('pages/index', $data);
            
        }

        public function upload($encUID = 0) {
            //Logout if no encrypted user id passed
            if ($encUID === 0) {
                redirectTo("users/logout");
                exit();
            } 

            //Decode user encrypted user id
            $id = getBase64DecodedValue(Constants::$session_key, $encUID);
            $id = $encUID;
            //query all categories
            $categories = $this->videoModel->queryCategories();
            $this->userObj = $this->userModel->getUserById($id);

            $data = [
                'title' => 'YuTube',
                'categories' => $categories,
                'encUID' => $encUID
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

        public function process($encUID) {
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
                                    "ian"
                                );


                $fileObj = new VideoProcessData($fileData);     
                //Create fileFullPath for the temporary file
                $tempFileFullPath = $fileObj->createFilePath(); 

                //Validate file
                $fileErrors = $fileObj->validateVideoFile($tempFileFullPath);
                if ( !empty($fileErrors) ) {
                    //Flash error message and redirect back to upload page
                    flash("file_upload_status",  $fileErrors[0], "alert alert-danger");
                    redirectTo('pages/upload/'. $this->encUID);
                    exit();
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

                            if ( $fileObj->saveVideoDetails($this->videoModel, $outputMp4FileFullPath, $formatted_duration) ) {
                         
                                //Generate thumbnails and save thumbnail details in the database
                                $lastInsertedVideoId = $this->videoModel->getLastInsertId();
                                if ( !($fileObj->generateThumbnails($this->videoModel, $outputMp4FileFullPath, $duration, $lastInsertedVideoId))) {
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
                                    redirectTo('pages/upload/' . $encUID);
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
                    redirectTo('pages/upload/' . $encUID);
                    exit();
                }     
            }
            else {
                //Redirect user to pages/upload if No POST data
                redirectTo('pages/upload/' . $encUID);
                exit();
            }
        }
    }
?>