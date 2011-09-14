<?php

    class Component_niceimage extends Pagefiller_default_component {
        
        //Controllers, Libraries, Helpers and Views work as expected
        
        //For adding Javascript and/or CSS to the <head> of the page, use
        // $this->add_css($name) and $this->add_js($name)
        
        //For getting the URL to a file in the media folder, use
        // $this->media_file_url($name)
        
        //also, see Wi3_component.php in the /wi3/application/libraries folder
        
        public function __construct() {
            if (Wi3::$editmode == true) {
                $this->css("style.css");
            }
        }
        
        public function event($eventname, $data) {
            if ($eventname == "field_added") {
                $field = $data["field"];
                $config = $data["config"];
                //save config to the handled field
                //in our case, this can be the filename that is to be loaded, or the width of the image
                foreach($config as $index => $value) {
                    $field->set($index, $value);
                }
            }
        }
      
        public function render_field($field) {
            $afb = $field->get("afbeelding");
            if (Wi3::$editmode) {
                $this->javascript("afbeelding_edit.js");
            }
            if (!empty($afb)) {
                $file = ORM::factory("file", $afb);
                $width = $field->get("width");
                if (empty($width)) {
                    $pagefillersconfig = Wi3::$config->site("pagefillers");
                    $width = $pagefillersconfig["pagefillers"]["default"]["pagefillerspecific"]["components"]["component_niceimage"]["width"];
                }
                return "<img src='" . Wi3::$urlof->image($file->filename,$width) . "' />";
            } else {
                return "<p style='padding: 10px;'><strong>dubbelklik om een afbeelding op te geven</strong></p>";
            }
        }
        
        function start_edit_field($field) {
           
            $images = Wi3::$files->find(array("whereExt" => Array("jpg", "png", "jpeg", "gif", "bmp")));
            $view = $this->view("afbeelding");
            $view->images = $images;
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => Array(
                    "fill" => Array("#wi3_editdiv_content" => $view->render())
                ),
                "scriptsafter" => Array("wi3_editdiv_show();")
            )));
        }
        
        function stopped_edit_field($field) {
            
            if (isset($_POST["afbeeldingid"])) {
                $field->set("afbeelding", $_POST["afbeeldingid"]);
            }
            
            //echo message for user and hide the edit_div
            echo str_replace("\\n", "", json_encode(Array(
                "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                //"alert" => "afbeelding ingesteld!",
                "scriptsafter" => Array("wi3_editdiv_hide();", "")
            )));
        }
        
        
        
    }

?>  