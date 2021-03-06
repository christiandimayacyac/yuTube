<?php
    class Pages extends Controller{
        private $videoModel;
        private $userModel;
        private $test;

        public function __construct() {
            //Load Models here
            $this->videoModel = $this->loadModel('video');
            $this->userModel = $this->loadModel('user');

            require_once "../app/classes/UserData.php";
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
            //Logout if no encrypted user id passed in the URL
            if ($encUID === 0) {
                redirectTo("users/logout");
                exit();
            } 

            checkFingerprint($encUID);

            //query all categories
            $categories = $this->videoModel->queryCategories();

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
                require_once APPROOT."/classes/VideoDataService.php";

                //Decode user encrypted user id
                $id = (int)getBase64DecodedValue(Constants::$session_key, $encUID);
                
                //Encapsulate all POST DATA containing the video file details
                $fileData = new VideoUploadData(
                                    $_FILES["inputFile"],
                                    $_POST["inputTitle"],
                                    $_POST["inputDescription"],
                                    $_POST["inputPrivacy"],
                                    $_POST["inputCategory"],
                                    $id
                                );
                //Create an instance of VideoDataService that will hold the file and details  
                //with corresponding methods in processing the file and details                                
                $fileObj = new VideoDataService($fileData);     
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

        public function watch($encUID="", $videoId=0) {

            //Check the presence of 2 paramters to detect guest user
            if ( $videoId === 0 && !empty($encUID) ) {

                //swap the value of $encUID to the value of $videoId
                $videoId = $encUID;

                //Check if there is a current session for a logged-in user
                if ( isset($_SESSION['uid']) ) {
                    
                    //set the $encUID based on the existing user session
                    $encUID = $_SESSION['uid'];

                    //redirect to the current page with the $encUID and videoId in the parameter
                    redirectTo('pages/watch/'. $encUID.'/'.$videoId);
                }
                else {
                    //set the current user as guest
                    $encUID = "guest";
                }
                
                
            }
            //If a possible user is detected, validate the $encUID
            elseif ( !empty($encUID) && !($videoId === 0) ) {
                // validate the $encUID parameter
                $decodedUID = $this->userModel->isEncUIDValid($encUID);
                //check for any special characters in the string
                $userInvalid = preg_match('/\D+/', $decodedUID);
                if ( $userInvalid==0 && !checkFingerprint($encUID) ) { //if $encUID is invalid redirect to 404
                    redirectTo("pages/pagenotfound");
                    exit();
                }
            }
            else {
                redirectTo("pages/pagenotfound");
                exit();
            }
            

            //Require the Video Class for CurrentVideo container
            require_once "../app/classes/CurrentVideo.php";
            
            //Instantiate current video
            $currentVideo = new CurrentVideo($this->videoModel, $videoId);

            //Check if video with given videoId exists; redirectTo 404 page if not found
            if ( $currentVideo->getVideoId() === null ) {
                redirectTo("pages/pagenotfound");
                exit();
            }

            //Instantiate User to get the Uploader details
            $currentVideoUploader = new User();
            $currentVideoUploader = $currentVideoUploader->getUserById($currentVideo->getVideoUploaderId());
            $encUploaderUID = getBase64EncodedValue(Constants::$data_key, $currentVideo->getVideoUploaderId());

            $data = [
                'title' => 'Player',
                'videoId' => $videoId,
                'currentVideo' => $currentVideo,
                'currentVideoUploader' => $currentVideoUploader,
                'loggedInUserId' => $encUID,
                'isSubscriber' => $this->userModel->isSubscribedTo($encUploaderUID, $encUID)
            ];

            $this->loadView("pages/watch", $data);
        }

        //Ajax Methods
        public function ajaxToggleSubscribe() {
            // $names = array("name"=>"ian", "gender"=>"male", "age"=>"38");
            $videoId = $_POST["videoId"];
            $btnClicked = $_POST["btnClicked"];
            $encUserId = $_POST["encUserId"];
            $encUploaderUID = $_POST["encUploaderId"];

            //check if current logged-in user is a subscriber of the uploader of the currently playing video
            $isSubscriber = $this->userModel->isSubscribedTo($encUploaderUID, $encUserId);

            //Toggle Subscribe and Unsubscribe
            if ( $isSubscriber ) {
                //Update db
                $result = $this->userModel->unsubscribeTo($encUploaderUID, $encUserId);
            }
            else {
                //Update db
                $result = $this->userModel->subscribeTo($encUploaderUID, $encUserId);
            }

            echo json_encode($result);
            // echo ($result);
        }

        public function ajaxLikeVideo() {
            $videoId =(int)$_POST["videoId"];
            $encUID = $_POST["encUID"];
            $userId = getBase64DecodedValue(Constants::$session_key,$encUID);
            $flag = "";

            //check if video is liked by the current user
            if ( $this->videoModel->videoLiked($videoId, $userId) ) {
                //Unlike the video
                $this->videoModel->decrementLikes($videoId, $userId);
            }
            else {
                //Like the video and Decrement dislike if user already disliked the video
                $this->videoModel->incrementLikes($videoId, $userId);
                $this->videoModel->decrementDislikes($videoId, $userId);
                $flag = "like";
            }

            $updatedNumOfLikes = $this->videoModel->getVideoLikes($videoId);
            $updatedNumOfDisLikes = $this->videoModel->getVideoDislikes($videoId);
            $likeDislikeStat = array("likes"=>$updatedNumOfLikes, "dislikes"=>$updatedNumOfDisLikes, "flag"=>$flag);
            echo (json_encode($likeDislikeStat));
        }

        public function ajaxDislikeVideo() {
            $videoId = (int)$_POST["videoId"];
            $encUID = $_POST["encUID"];
            $userId = getBase64DecodedValue(Constants::$session_key,$encUID);
            $flag = "";

            //check if video is disliked by the current user
            if ( $this->videoModel->videoDisliked($videoId, $userId) ) {
                //Undislike the video
                $this->videoModel->decrementDislikes($videoId, $userId);
            }
            else {
                //Like the video and Decrement dislike if user already disliked the video
                $this->videoModel->incrementDislikes($videoId, $userId);
                $this->videoModel->decrementLikes($videoId, $userId);
                $flag = "dislike";
            }

            $updatedNumOfLikes = $this->videoModel->getVideoLikes($videoId);
            $updatedNumOfDisLikes = $this->videoModel->getVideoDislikes($videoId);
            $likeDislikeStat = array("likes"=>$updatedNumOfLikes, "dislikes"=>$updatedNumOfDisLikes, "flag"=>$flag);
            echo (json_encode($likeDislikeStat));
        }

        public function pagenotfound() {
            $data = [
                'title' => '404 Page not Found',
                'developer' => 'Christian Dimayacyac'
            ];
            $this->loadView('pages/404', $data);
        }


    }
?>