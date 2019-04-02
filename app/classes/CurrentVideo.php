<?php
/**
 * This Class serves as a video object that holds current video details
 */
    class CurrentVideo {
        private $videoId;
        private $videoTitle;
        private $videoDescription;
        private $videoPrivacyType;
        private $videoFilePath;
        private $videoCategory;
        private $videoDateUploaded;
        private $videoViews;
        private $videoDuration;
        private $videoUploaderId;
        private $videoLikes;
        private $videoDislikes;
        private $video;

        private $videoModel;

        public function __construct($videoModel, $videoId) {
            $this->videoModel = $videoModel;
            $this->video = $videoModel->getVideoById($videoId);
            
            $this->videoId = $this->video->id;
            $this->videoTitle = $this->video->title;
            $this->videoDescription = $this->video->description;
            $this->videoPrivacyType = $this->video->privacy;
            $this->videoFilePath = $this->video->filePath; 
            $this->videoCategory = $this->video->category;
            $this->videoDateUploaded = $this->video->dateUploaded;
            $this->videoViews = $this->video->views;
            $this->videoDuration = $this->video->duration;
            $this->videoUploaderId = $this->video->uploadedBy;
            $this->videoLikes= $this->video->likes;
            $this->videoDislikes = $this->video->dislikes;
        }

        public function getVideoId() {
            return $this->videoId;
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

        public function getVideoFilePath() {
            return $this->videoFilePath;
        }

        public function getVideoCategory() {
            return $this->videoCategory;
        }

        public function getVideoDateUploaded() {
            return $this->videoDateUploaded;
        }
        public function getVideoViews() {
            return $this->videoViews;
        }
        public function getVideoDuration() {
            return $this->videoDuration;
        }
        public function getVideoUploaderId() {
            return $this->videoUploaderId;
        }

        public function isVideoLiked($userId) {
            return $this->videoModel->videoLiked($this->videoId, $userId);
        }

        public function isVideoDisliked($userId) {
            return $this->videoModel->videoDisliked($this->videoId, $userId);
        }

        public function getVideoLikes() {
            return $this->videoModel->getVideoLikes($this->videoId);
        }

        public function getVideoDislikes() {
            return $this->videoModel->getVideoDislikes($this->videoId);
        }
    }
?>