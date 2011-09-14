<?php

    class Pagefiller_default_componentinput { 
    
        public $componenttype;
        
        public $inputtype;
        public $inputname;
    
        public function __construct($inputtype, $inputname, $settings)
        {
            $this->inputtype = $inputtype;
            $this->inputname = $inputname;
            if (!isset($settings["componenttype"]) OR empty($settings["componenttype"]))
            {
                // Well, this is not a problem as of yet...
            }
        }
        
        public static function factory($inputtype, $inputname, $settings = array())
        {
            $new = new self($inputtype, $inputname, $settings);
            return $new;
        }
        
        public function render()
        {
            if ($this->inputtype == "image")
            {
                // Find all images and display them
                $id = Wi3::date_now();
                $images = Wi3::$files->find(array("whereExt" => Array("jpg", "png", "jpeg", "gif", "bmp")));
                $return = "";
                $counter = 0;
                foreach($images as $image) {
                    $counter++;
                    $return .= "<div style='float: left;' id='image_".$id."_".$counter."' class='image_".$id."'>";
                        $return .= "<a href='javascript:void(0)' style='text-decoration: none;' onClick='$(\"#input_".$id."\").val(\"".$image->url."\").has(\"xyz\").add(\"#image_".$id."_".$counter."\").fadeTo(50,1).has(\"xyz\").add(\".image_".$id."\").not(\"#image_".$id."_".$counter."\").fadeTo(50,0.40);'>";
                        $return .= "<div style='float: left; margin: 5px; '><img src='" . Wi3::$urlof->image($image->filename, 50) . "'></img></div></a>";
                    $return .= "</div>";
                }
                
                return "<input id='input_".$id."' style='display: none;' value='test' name='" . $this->inputname . "'/>" . $return;
            }
        }
    
    }

?>