<?php
class Video {
    private $db;
    private $lastId;

    public function __construct() {
        $this->db = new Database;
    }


    public function queryVideos() {
        return "Sample video";
    }

    public function queryCategories() {
        $this->db->query("SELECT * FROM categories");
        $this->db->execute();

        return $this->db->getResultSet();
    }

    public function getLastInsertId() {
        return $this->db->getLastInsertId();
    }


    public function insertVideoDetails($videoDetails, $outputMp4FileFullPath, $duration) {
        $this->db->query("INSERT INTO videos(
                                        uploadedBy, 
                                        title, 
                                        description, 
                                        privacy, 
                                        filePath, 
                                        category, 
                                        duration
                                        )
                            VALUES(
                                :uploadedBy, 
                                :title, 
                                :description, 
                                :privacy, 
                                :filePath, 
                                :category,
                                :duration
                            )
                         ");
        $this->db->bind(":uploadedBy", $videoDetails->getvideoUploaderId());
        $this->db->bind(":title", $videoDetails->getVideoTitle());
        $this->db->bind(":description", $videoDetails->getVideoDescription());
        $this->db->bind(":privacy", $videoDetails->getVideoPrivacyType());
        $this->db->bind(":filePath", $outputMp4FileFullPath);
        $this->db->bind(":category", $videoDetails->getVideoCategory());
        $this->db->bind(":duration", $duration);
        
        $this->db->execute();

        if ( $this->db->getRowCount() > 0 ) {
            $this->lastId = $this->db->getLastInsertId();
            return true;
        }
        else {
            return false;
        }

    }

    public function insertThumbnails($videoId, $thumbFullPath, $selected) {
        $this->db->query("INSERT INTO thumbnails(videoId, filePath, selected) VALUES(:videoId, :filePath, :selected)");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":filePath", $thumbFullPath);
        $this->db->bind(":selected", $selected);
        $this->db->execute();

        if ( $this->db->getRowCount() > 0 ) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getVideoById($videoId) {
        $this->db->query("SELECT * FROM videos WHERE id = :videoId");
        $this->db->bind(":videoId", $videoId);
        
        return $this->db->getResultRow();
    }

    public function incrementViews($videoId) {
        $this->db->query("UPDATE videos SET views=views+1 WHERE id = :videoId");
        $this->db->bind(":videoId", $videoId);
        $this->execute();

        return $this->getRowCount();

        //TODO: update video object model once number of views gets incremented
    }

    public function incrementLikes($videoId, $userId) {
        //Insert Like record into the likes table
        $this->db->query("INSERT INTO likes(videoId, userId) VALUES(:videoId, :userId)");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":userId", $userId);
        $this->db->execute();

        // Update the total likes of the video being liked in the videos table
        if ( $this->db->getRowCount() > 0 ) {
            $this->db->query("UPDATE videos SET likes=likes+1 WHERE id = :videoId");
            $this->db->bind(":videoId", $videoId);
            $this->db->execute();
        } 

        return $this->db->getRowCount();

        //TODO: update video object model once number of likes gets incremented
    }

    public function decrementLikes($videoId, $userId) {
        //Remove Like record from the likes table
        $this->db->query("DELETE FROM likes WHERE videoId = :videoId AND userId = :userId");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":userId", $userId);
        $this->db->execute();

        //Update the total likes of the video being unliked in the videos table
        if ( $this->db->getRowCount() > 0 ) {
            $this->db->query("UPDATE videos SET likes=likes-1 WHERE id = :videoId");
            $this->db->bind(":videoId", $videoId);
            $this->db->execute();
        }

        return $this->db->getRowCount();

        //TODO: update video object model once number of likes gets decremented
    }
    
    public function incrementDislikes($videoId, $userId) {
        //Insert Dislike record into the likes table
        $this->db->query("INSERT INTO dislikes(videoId, userId) VALUES(:videoId, :userId)");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":userId", $userId);
        $this->db->execute();

        //Update the total dislikes of the video being liked in the videos table
        if ( $this->db->getRowCount() > 0 ) {
            $this->db->query("UPDATE videos SET dislikes=dislikes+1 WHERE id = :videoId");
            $this->db->bind(":videoId", $videoId);
            $this->db->execute();
        }

        return $this->db->getRowCount();

        //TODO: update video object model once number of likes gets incremented
    }
    
    public function decrementDislikes($videoId, $userId) {
        //Remove Dislike record from the dislikes table
        $this->db->query("DELETE FROM dislikes WHERE videoId = :videoId AND userId = :userId");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":userId", $userId);
        $this->db->execute();
        
        //Update the total dislikes of the video being disliked in the videos table
        if ( $this->db->getRowCount() > 0 ) {
            $this->db->query("UPDATE videos SET dislikes=dislikes-1 WHERE id = :videoId");
            $this->db->bind(":videoId", $videoId);
            $this->db->execute();
        }

        return $this->db->getRowCount();

        //TODO: update video object model once number of dislikes gets incremented
    }

    public function videoLiked($videoId, $userId) {
        $this->db->query("SELECT id FROM likes WHERE videoId = :videoId AND userId = :userId");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":userId", $userId);
        $this->db->execute();

        return $this->db->getResultRow();
    }

    public function videoDisliked($videoId, $userId) {
        $this->db->query("SELECT id FROM dislikes WHERE videoId = :videoId AND userId = :userId");
        $this->db->bind(":videoId", $videoId);
        $this->db->bind(":userId", $userId);
        $this->db->execute();

        return $this->db->getResultRow();
    }

    public function getVideoLikes($videoId) {
        $this->db->query("SELECT likes FROM videos WHERE id = :videoId");
        $this->db->bind(":videoId", $videoId);
        $numOfLikes = $this->db->getResultRow(); 

        return $numOfLikes->likes;
    }

    public function getVideoDislikes($videoId) {
        $this->db->query("SELECT dislikes FROM videos WHERE id = :videoId");
        $this->db->bind(":videoId", $videoId);
        $numOfDislikes = $this->db->getResultRow(); 
        
        return $numOfDislikes->dislikes;
    }


    
    
}

?>