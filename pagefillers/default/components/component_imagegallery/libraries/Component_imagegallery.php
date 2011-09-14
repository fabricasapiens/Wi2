<?php

    class Component_imagegallery extends Pagefiller_default_component {
        
        public $wi3_cacheable = false; //this field is cacheable by default. Can be set to false in constructor (Cache will always instantiate, then check for this var)
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_4_2_core"); //plugins that need to be loaded before this component can do its work
        
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
        
        public function __construct() {
            if (Wi3::$editmode == true) {
                //if we are in editmode, we will need the following to be enabled
                // - JQuery UI
                // - TinyMCE (for rich text editing of parts of the items)
                // - Wi3 client <> Wi3 server communication
                // The JQuery Core will be autoloaded
                $this->wi3_dependencies_plugins = array( "Plugin_jquery_1_4_2_wi3", "Plugin_jquery_1_4_2_ui", "Plugin_tinymce");
                //load javascript that is needed for editing
                $this->javascript("edit.js");
            }
            //load css (load it in the __construct, so that it still gets included in <head> even though this field's content might not be rendered but fetched from Cache)
            $this->javascript("view.js");
            $this->css("style.css");
            parent::__construct(); //here, the required plugins will be loaded. We could alternatively also just do Wi3::$plugins->load("Plugin_jquery_1_3_2");
        }
        
        // --------------------------------------------------
        // fetch all the items belonging to a certain field
        // --------------------------------------------------
        public function get_all_items($field) {
            return $field->find_sqlarray( 
                array("where" => array(
                    "arraygroup" => "items"
                ))
            );
        }
        
        // --------------------------------------------------
        // fetch one particular item belonging to a certain field
        // --------------------------------------------------
        public function get_item($field, $where = array()) {
            if (!is_array($where)) {
                $where = array();
            }
            //add group clausule
            $where["arraygroup"] = "items";
            //now get that item
            return $field->get_sqlarray( 
                array("where" => $where)
            );
        }
        
        // --------------------------------------------------
        // standard function to render a certain field with this component
        // $field is always a valid Field Object
        // --------------------------------------------------
        public function render_field($field) {
            //fetch all stored items and pass them to the view
            //$items = $this->get_all_items($field);
            $folderid = $field->get("folderid");
            if (!empty($folderid) AND $folderid > 0) {
                $items = Wi3::$files->findRecursive(Array("whereExt" => Array("jpg","png","gif","jpeg"), "withFolders" => TRUE, "whereFolder" => $folderid));
            } else {
                $items = Wi3::$files->findRecursive(Array("whereExt" => Array("jpg","png","gif","jpeg"), "withFolders" => TRUE, ));
            }
            $view = $this->view("view");
            $view->set("c", "component_imagegallery");
            $view->set("field", $field);
            $width = $field->get("width");
            if (empty($width)) { $width = 500; } //default size
            $view->set("width", $width);
            $view->set("images", $items);
            return $view->render(TRUE);
        }
        
        // --------------------------------------------------
        // standard function to return an edit-mode for a particular field
        // this function is called via AJAX !
        // and should thus return JSON
        // $field is always a valid Field Object
        // --------------------------------------------------
        function start_edit_field($field) {
            //fetch all stored items and pass them to the view
            $items = $this->get_all_items($field);
            $view = $this->view("edit");
            $view->set("field", $field);
            $view->set("items", $items);
            //return JSON
            //with which this component is also initiated for editing
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => Array(
                    "fill" => Array("#wi3_editdiv_content" => $view->render())
                ),
                "scriptsafter" => Array("wi3_editdiv_show();", "wi3.pagefiller.components." . $field->type . ".initedit();")
            )));
        }
        
        // --------------------------------------------------
        // standard function to return code that indicates the end of the edit-mode for this field
        // this function is called via AJAX !
        // and should thus return JSON
        // $field is always a valid Field Object
        // --------------------------------------------------
        function stopped_edit_field($field) {
            
            //all editing has been done via AJAX when edit-mode was open
            //thus, there is no need to save/edit things or empty caches, as that has already been done while editing
            
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "data succesvol opgeslagen",
                "scriptsbefore" => Array("wi3.tinymce.destroy('" . $field->htmlid() . "');")
            )));
        }
        
    }
    
?>
