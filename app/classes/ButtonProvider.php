<?php
    class ButtonProvider {

        public static function createButton($class, $action, $text, $imageSrc, $alt='', $disabled='') {
            //check for optional $imageSrc
            $imageSrc = ($imageSrc == null) ? "" : "<img src='$imageSrc' alt='$alt button'>";

            return "<button class='$class' onclick='$action'$disabled>
                        $imageSrc
                        <span class='button-text'>$text</span>
                    </button>";
        }
    }
?>