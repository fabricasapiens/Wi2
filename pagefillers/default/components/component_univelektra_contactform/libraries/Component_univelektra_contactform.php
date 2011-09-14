<?php

    class Component_univelektra_contactform extends Pagefiller_default_component  {
        
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_4_2_core", "Plugin_jquery_1_4_2_wi3"); //plugins that need to be loaded before this component can do its work
        
        //render a certain field
        //$field is always a valid Field Object
        //@ return : content of the field in text-form (if handling with a View-object, then Object->render()!)
        public function render_field($field) {
            //render contactform
            //$a =  new Profiler();
            
            if (defined("WI3_EDITMODE")) { 
                $this->javascript("component_default_contactform_edit.js");
            } 
            //and always include the standard function(s)
            $this->javascript("component_default_contactform.js");
            
            return $this->view("component_default_contactform")->render();
        }
        
        //is called when a user starts to edit a field
        //@return : JSON
        function start_edit_field($field) {
            $fs = new FileStorage();
            if (defined("WI3_EDITMODE")) { 
                $this->javascript("component_default_contactform_edit.js");
            } 
            //and always include the standard function(s)
            $this->javascript("component_default_contactform.js");
            $view = $this->view("component_default_contactform_edit");
            $view->emailaddress = $fs->get("wi3_field_" . $field->id);
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => Array(
                    "fill" => Array("#wi3_editdiv_content" => $view->render())
                ),
                "scriptsafter" => Array("wi3_editdiv_show();")
            )));
        }
        
        //is called when the user has stopped editing a field
        function stopped_edit_field($field) {
            return $this->save_email($field->id);
        }
        
        //edit-user wants to save the to-emailaddress
        //@return : JSON
        function save_email($fieldid) {
            //save emailaddress (which is sent along in the $_POST)
            $fs = new FileStorage();
            $fs->set("wi3_field_" . $fieldid, $_POST["emailaddress"]);
            //echo message for user and hide the edit_div
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "emailaddress saved to " . $_POST["emailaddress"] . ".",
                "scriptsafter" => Array("wi3_editdiv_hide();")
            )));
        }
        
        //site-visitor sends a message
         //@return : JSON
        function send_message($fieldid) {
            //get the emailaddress we are ought to send the mail to
            $fieldid = substr($fieldid, 10);
            if (is_numeric($fieldid)) {
                $field = ORM::factory("field", $fieldid);
                if ($field) {
                    $fs = new FileStorage();
                    $emailaddress = $fs->get("wi3_field_" . $field->id);
                    $header = "From: ". Input::xss_clean($_POST["name"]) . " <" . Input::xss_clean($_POST["email"]) . ">\r\n"; //optional headerfields
                    mail($emailaddress, $_POST["subject"], $_POST["message"], $header);
                    echo str_replace("\\n", "", json_encode(Array(
                        "alert" => "message has been sent!",
                        "scriptsafter" => Array("")
                    )));
                }
            }
        }
        
    }
    
?>