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
    
    
}

?>