<?php
/** This class generates the video info section i.e. 
 * video title, description, # of views, likes and dislikes , etc
 */
    class VideoInfoSection {
        private $encUID;
        private $loggedInUserId;
        private $currentVideo;
        private $currentVideoUploader;
        private $isSubscriber;

        public function __construct($encUID, $currentVideo, $currentVideoUploader, $isSubscriber) { //$isSubscriber returns true if the current logged-in user is a subscriber of the owner the currently playing video
            $this->encUID = $encUID;
            if ( $encUID !== "guest" ) {
                $this->loggedInUserId = getBase64DecodedValue(Constants::$session_key,$encUID);
            }
            else {
                $this->loggedInUserId = $encUID;
            }
            // $this->loggedInUserId = $encUID;
            $this->currentVideo = $currentVideo;
            $this->currentVideoUploader = $currentVideoUploader;
            $this->isSubscriber = $isSubscriber;

            //reuqire ButtonProvider to generate button
            require_once "../app/classes/ButtonProvider.php";
        }

        public function create() {
            return $this->getPrimaryVideoInfo() . $this->getSecondaryVideoInfo();
        }

        /**
         * Generates the primary section of the video info
         */
        public function getPrimaryVideoInfo() {
            require_once "../app/classes/LikeDislikeButtonsProvider.php";

            // $likeDislikeButtons = new LikeDislikeButtonsProvider($this->currentVideo, $this->encUID);
            $likeDislikeButtons = new LikeDislikeButtonsProvider($this->currentVideo, $this->encUID);


            return "<div class='video-primary-info'>
                        <h2 class='video-title'>{$this->currentVideo->getVideoTitle()}</h2>
                        <p class='video-stats'>
                            <span class='video-views'>{$this->currentVideo->getVideoViews()} views</span>
                            {$likeDislikeButtons->create()}
                        </p>
                    </div>";
        }
        
        /**
         * Generates the secondary section of the video info
         */
        public function getSecondaryVideoInfo() {
            //Retrieve and format the date video was uploaded
            $date = $this->currentVideo->getVideoDateUploaded();
            $date = date('M d, Y', strtotime($date));

            $numOfSubscribers = $this->currentVideoUploader->subscribers;
            //Check if no subscribers; Set to empty if none;
            if  ( (int)$numOfSubscribers === 0 ) {
                $numOfSubscribers = "";
            }
            
            $buttonText = "SUBSCRIBE";
            $disabled = "";  
            $buttonClass = "video-subscibe-button subscribe";
            $profilePicPath = '../../../'. $this->currentVideoUploader->profilePicPath;
            //Check if a user is logged in; Disable button if not logged in
            // if ( !isUserLoggedIn($this->encUID) || ($this->loggedInUserId !== $this->currentVideoUploader->userId) ) {
            if ( !isUserLoggedIn($this->encUID) ) {
                $disabled = " disabled";
                $profilePicPath = '../../'. $this->currentVideoUploader->profilePicPath;
                // die("1");
            }
            elseif ( $this->loggedInUserId === $this->currentVideoUploader->userId ) {
                $buttonText = "Edit Video";
                $buttonClass = "video-subscibe-button";
                $numOfSubscribers = "";   
                // $disabled = "";
                // $profilePicPath = '../../../'. $this->currentVideoUploader->profilePicPath;
                // die("2");
            }
            else {
                // check if user is already subscibed in the uploader of the currently playing video
                if( $this->isSubscriber ) {
                    $buttonText = "SUBSCRIBED";
                } 
            }

            $encryptedUploaderID = getBase64EncodedValue(Constants::$data_key, $this->currentVideoUploader->userId);
            $subscribeAction = "toggleSubscribe(this, {$this->currentVideo->getVideoId()}, \"$this->encUID\", \"$encryptedUploaderID\")";
            $subscribeButton = ButtonProvider::createButton($buttonClass, $subscribeAction, "$buttonText $numOfSubscribers", null, "Subscribe", $disabled);
            
            $profileLink = "../../../users/profile/$encryptedUploaderID";

            //TODO: Format number of subscribers if 1K and above
            return "<div class='video-secondary-info'>
                        <div class='video-secondary-avatar'>
                            <a href='$profileLink' class='video-avatar-link'>
                                <img src='$profilePicPath' alt='user avatar'>
                            </a>
                        </div>
                        <div class='video-secondary-text'>
                            <h2 class='video-uploader'><a href='$profileLink'>{$this->currentVideoUploader->username}</a></h2>
                            <p class='video-published'>Published on $date</p>
                            <p class='video-description'>{$this->currentVideo->getVideoDescription()}</p>
                        </div>
                        $subscribeButton
                    </div>";
        }
    }

?>