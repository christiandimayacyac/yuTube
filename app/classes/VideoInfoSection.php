<?php
/** This class generates the video info section i.e. 
 * video title, description, # of views, likes and dislikes , etc
 */
    class VideoInfoSection {
        private $encUID;
        private $loggedInUserId;
        private $currentVideo;
        private $currentVideoUploader;

        public function __construct($encUID, $currentVideo, $currentVideoUploader) {
            $this->encUID = $encUID;
            // $this->loggedInUserId = getBase64DecodedValue(Constants::$session_key,$encUID);
            $this->loggedInUserId = $encUID;
            $this->currentVideo = $currentVideo;
            $this->currentVideoUploader = $currentVideoUploader;

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

            // $likeDislikeButtons = new LikeDislikeButtonsProvider($this->currentVideo, $this->loggedInUserId);
            $likeDislikeButtons = new LikeDislikeButtonsProvider($this->currentVideo, $this->loggedInUserId);


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
            $date = new DateTime($this->currentVideo->getVideoDateUploaded());
            $date = $date->format('F d, Y');

            $button = "SUBSCRIBED";
            $numOfSubscribers = $this->currentVideoUploader->subscribers;

            // $subscribeAction = "toggleSubscribe(this, {$this->currentVideo->getVideoId()}, {$this->loggedInUserId})";
            $subscribeAction = "toggleSubscribe(this, {$this->currentVideo->getVideoId()}, {$this->loggedInUserId})";
            $subscribeButton = ButtonProvider::createButton("video-subscibe-button", $subscribeAction, "$button $numOfSubscribers", null, "Subscribe");

            //TODO: Format number of subscribers if 1K and above
            return "<div class='video-secondary-info'>
                        <div class='video-secondary-avatar'>
                            <img src='../../../{$this->currentVideoUploader->profilePicPath}' alt='user avatar'>
                        </div>
                        <div class='video-secondary-text'>
                            <h2 class='video-uploader'>{$this->currentVideoUploader->username}</h2>
                            <p class='video-published'>Published on $date</p>
                        </div>
                        $subscribeButton
                    </div>
                    ";
        }
    }

?>