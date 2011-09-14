<?php

    class Component_text extends Pagefiller_default_component {
        
        public $wi3_cacheable = true; //this field is cacheable by default. Can be set to false in constructor (Cache will always instantiate, then check for this var)
        public $wi3_dependencies_plugins = array(); //plugins that need to be loaded before this component can do its work
        
        public function __construct() {
            if (Wi3::$editmode == true) {
                //if we are in editmode, we will need JQuery to be enabled, as well as the wi3<>kohana communication module
                $this->wi3_dependencies_plugins = array( "Plugin_jquery_1_4_2_core", "Plugin_jquery_1_4_2_wi3", "Plugin_tinymce");
            }
            parent::__construct(); //here, the required plugins will be loaded. We could alternatively also just do Wi3::$plugins->load("Plugin_jquery_1_3_2");
        }
        
        //render a certain field
        //$field is always a valid Field Object
        public function render_field($field) {
            //get content of field, based on type
            $content = $field->get("text");
            if (empty($content)) {
                if (Wi3::$editmode == true) {
                    $content = "dubbelklik om te wijzigen";
                }
            }
            return $content;
        }
        
        //is called when a user starts to edit a field
        function start_edit_field($field) {
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "veld kan gewijzigd worden",
                "dom" => array(
                    "remove" => "document"
                ),
                "scriptsbefore" => Array("wi3_tinymce_init_textmodule('" . $field->htmlid() . "', 'advanced');")
            )));
        }
        
        //is called when the user has stopped editing a field
        function stopped_edit_field($field) {
            
            //if text is changed, the cache should be reloaded
            Wi3::$cache->field_delete_all_wherefield($field);
            
            //save content
            $field->set("text",  $_POST["data"]);
            
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "data succesvol opgeslagen",
                "scriptsbefore" => Array("wi3.tinymce.destroy('" . $field->htmlid() . "');"),
                //"dom" => array(
                //    "fill" => array("#wi3_field_".$field->id, $this->render_field($field))
                //)
            )));
        }
        
        function event($eventname, $data) {
            if ($eventname == "upgrade") {
                if ($data["from"]*1 == 1 AND $data["to"]*1 == 2) {
                    //rename the text field that is used for this field
                    //it first was in the order of wi3_field_91~~0
                    //and it should be in the order of wi3_Field_Model_91_text~~0
                    $fs = new FileStorage();
                    $field = $data["field"];
                    if ($fs->exists("wi3_field_" . $field->id . "")) {
                        $content = $fs->get("wi3_field_" . $field->id . "");
                        //now change the location of the links in the text from the old /users/username
                        //to the new /wi3/sites/username!
                        preg_replace("@/users/" . Wi3::$user->username . "/@", "/wi3/sites/" . Wi3::$user->username, $content);
                        $fs->set("wi3_Field_Model_" . $field->id . "_text", $content);
                        $fs->delete("wi3_field_" . $field->id . "");
                    }
                }
            }
        }
        
    }
    
?>
