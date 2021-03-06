<?php 
    class VideoDetailsFormProvider {
        private $categories;

        public function __construct($categories) {
            $this->categories = $categories;
        }
        
        public function createUploadForm($encUID) {
            $fileInput = $this->createInputFile();
            $titleInput = $this->createInputTitle();
            $DescriptionInput = $this->createInputDescription();
            $submitButton = $this->createSubmitButton();
            $selectPrivacy = $this->createSelectPrivacy();
            $selectCategory = $this->createSelectCategory();

            return "<form action='../process/" . $encUID ."' method='POST' enctype='multipart/form-data'>
                        $fileInput
                        $titleInput
                        $DescriptionInput
                        $selectPrivacy
                        $selectCategory
                        $submitButton
                    </form>";
        }

        private function createInputFile() {
            return "<div class='form-group'>
                        <input type='file' class='form-control-file' id='inputFile' name='inputFile'>
                    </div>";
        }

        private function createInputTitle() {
            return "<div class='form-group'>
                        <label for='inputTitle'>Title</label>
                        <input type='text' class='form-control' id='inputTitle' name='inputTitle'>
                    </div>";
        }

        private function createInputDescription() {
            return "<div class='form-group'>
                        <label for='inputDescription'>Description</label>
                        <textarea class='form-control' id='inputDescription' rows='3' name='inputDescription'></textarea>
                    </div>";
        }

        private function createSelectPrivacy() {
            return "<div class='form-group'>
                        <select class='custom-select' id='inputPrivacy' name='inputPrivacy'>
                            <option value='1' selected>Private</option>
                            <option value='2'>Public</option>
                        </select>
                    </div>";
        }
        
        private function createSelectCategory() {
            //reiterate each of the categories
            $html = "<div class='form-group'><select class='custom-select' id='inputCategory' name='inputCategory'>";
            foreach($this->categories as $category) {
                $html .= "<option value=$category->cat_id>$category->cat_name</option>"; 
            }
            $html .= "</select></div>";
                    
            return $html;   
        }

        
        
        private function createSubmitButton() {
            return "<button type='submit' class='btn btn-primary' name='inputSubmit' id='inputSubmit'>Upload</button>";
        }
    }

?>
