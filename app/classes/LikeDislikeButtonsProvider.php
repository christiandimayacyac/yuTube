<?php
    //Require ButtonProvider class to create Like and Dislike buttons
    require_once APPROOT."/classes/ButtonProvider.php";

    class LikeDislikeButtonsProvider {
        private $currentVideo;
        private $encUID;
        private $loggedInUserId;
        private $videoId;

        // public function __construct($currentVideo, $loggedInUserId) {
        public function __construct($currentVideo, $encUID) {
            $this->currentVideo = $currentVideo;
            $this->videoId = $currentVideo->getVideoId();
            // $this->loggedInUserId = $loggedInUserId;
            $this->encUID = $encUID;
            if ( $encUID !== "guest" ) {
                $this->loggedInUserId = getBase64DecodedValue(Constants::$session_key,$encUID);
            }
            else {
                $this->loggedInUserId = $encUID;
            }
            // $this->loggedInUserId = getBase64DecodedValue(Constants::$session_key,$encUID);
        }

        public function create() {
            $class = "thumbs-icon";
            //Check if current video is liked by the logged-in user
            //Set appropriate icon
            if ( $this->encUID == "guest" ) {
                $thumbsUpIcon = "../../images/icons/thumb-up.png";
                // $class = "thumbs-icon";
            }
            elseif ( $this->currentVideo->isVideoLiked($this->loggedInUserId) === FALSE || !isUserLoggedIn($this->encUID) ) {
                $thumbsUpIcon = "../../../images/icons/thumb-up.png";
                // $class = "thumbs-icon";
            }
            else {
                $thumbsUpIcon = "../../../images/icons/thumb-up-active.png";
                $class = "thumbs-icon active";
            }

            //Set appropriate icon
            if ( $this->encUID == "guest" ) {
                $thumbsDownIcon = "../../images/icons/thumb-down.png";
                // $class = "thumbs-icon";
            }
            elseif ( $this->encUID == "guest" || $this->currentVideo->isVideoDisliked($this->loggedInUserId) === FALSE || !isUserLoggedIn($this->encUID) ) {
                $thumbsDownIcon = "../../../images/icons/thumb-down.png";  
                // $class = "thumbs-icon";
            }
            else {
                $thumbsDownIcon = "../../../images/icons/thumb-down-active.png";  
                $class = "thumbs-icon active";
            }

            $likeButton = $this->createLikeButton($thumbsUpIcon, $class);
            $dislikeButton = $this->createDislikeButton($thumbsDownIcon, $class);
            $numOfLikes = $this->currentVideo->getVideoLikes($this->videoId);
            $numOfDislikes = $this->currentVideo->getVideoDislikes($this->videoId);

            //Set number of likes to empty string if = to zero
            if ( $numOfLikes <= 0 ) {
                $numOfLikes = "";
            }

            //Set number of dislikes to empty string if = to zero
            if ( $numOfDislikes <= 0 ) {
                $numOfDislikes = "";
            }
            
            return "<span class='thumbs'>
                        $likeButton <span class='likeCount'>$numOfLikes</span>
                        $dislikeButton <span class='dislikeCount'>$numOfDislikes</span>
                    </span>";
        }

        public function createLikeButton($thumbsUpIcon, $class) {
            if ( $this->encUID === "guest" ) {
                $action = "";
                return ButtonProvider::createButton($class, $action, "", $thumbsUpIcon, "Like","disabled");
            }
            else {
                $action = (isUserLoggedIn($this->encUID)) ? "likeVideo({$this->currentVideo->getVideoId()}, \"{$this->encUID}\")" : "";
                return ButtonProvider::createButton($class, $action, "", $thumbsUpIcon, "Like");
            }
            
        }
        
        public function createDislikeButton($thumbsDownIcon, $class) {
            if ( $this->encUID === "guest" ) {
                $action = "";
                return ButtonProvider::createButton($class, $action, "", $thumbsDownIcon, "Dislike", "disabled");
            }
            else {
                $action = (isUserLoggedIn($this->encUID)) ? "dislikeVideo({$this->currentVideo->getVideoId()}, \"{$this->encUID}\")" : "";
                return ButtonProvider::createButton($class, $action, "", $thumbsDownIcon, "Dislike");
            }
            
        }
    }

?>