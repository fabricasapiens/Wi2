<?php

    class Component_forms extends Pagefiller_default_component {
        
        public $wi3_cacheable = false; //this field is cacheable by default. Can be set to false in constructor (Cache will always instantiate, then check for this var)
        public $wi3_dependencies_plugins = array(); //plugins that need to be loaded before this component can do its work
        
        public function __construct() {
            if (Wi3::$editmode == true) {
                //if we are in editmode, we will need JQuery to be enabled, as well as the wi3<>kohana communication module
                $this->wi3_dependencies_plugins = array( "Plugin_jquery_1_4_2_core", "Plugin_jquery_1_4_2_wi3", "Plugin_jquery_1_4_2_ui", "Plugin_tinymce");
                //load javascript that is needed for editing
                $this->javascript("component_forms_edit.js");
            }
            //load css
            $this->css("component_forms.css");
            parent::__construct(); //here, the required plugins will be loaded. We could alternatively also just do Wi3::$plugins->load("Plugin_jquery_1_3_2");
        }
        
        //render a certain field
        //$field is always a valid Field Object
        public function render_field($field) {
            //load the current form as an sqlarray
            $form = $field->get_sqlarray( array("where"=>array("arrayname" => "form")) );
            if (!is_object($form)) {
                $form = $field->new_sqlarray( array("arrayname" => "form") );
                $form->save();
            }
            
            //load thankyoumessage
            $thankyoumessage = $field->get("thankyoumessage");
            
            //load view and pass form and thankyoumessage to it
            $view = $this->view("forms");
            
            //load form and check for validation
            $f = Formo::factory()->plugin('auto_i18n')->plugin('csrf');
            //make the form look pretty in a table
            // or if you need it set globally but only for this one form
            
            //no submit is yet present
            $submitadded = false;
            //loop through each form-element
            foreach($form as $naam => $array) {
                $array = unserialize($array); //unserialize the stored form-info
                if (!is_array($array)) {
                    //corrupt
                    unset($form->$naam);
                    $form->save();
                    continue;
                }
                //check if there is a submit
                if ($array["type"] == "submit") {
                    $submitadded = TRUE; //so we don't have to add a submit ourselves, at the bottom
                }
                //default type is text
                if (!isset($array["type"])) {
                    $array["type"] = "text";
                }
                //if title is not set, use name as title
                if (!isset($array["title"]) or empty($array["title"])) {
                    $array["title"] = $naam;
                }
                //if type is selectbox, then load the options
                if ($array["type"] == "select") {
                    $options = $array["options"];
                    $lastadded = $f->add("select", $array["name"], array("values" => $options, "label"=> $array["title"]));
                } else if ($array["type"] == "html" OR $array["type"] == "submit") { 
                    $lastadded = $f->add($array["type"], $array["name"], $array["title"]);
                } else {
                    $lastadded = $f->add($array["type"], $array["name"], array("label" => $array["title"]));
                }
                //check if required is neccessary (default is FALSE)
                if (isset($array["required"]) AND $array["required"] == true) {
                    $lastadded->required(TRUE);
                }
                //$lastadded->label_open("<h3 style='width: 200px;'>");
                //$lastadded->label_close("</h3>");
            }
            //check if there was an element with a submit-button, and if not, add one ourselves
            if ($submitadded == false) {
                $f->add("submit", "_submit", "formulier verzenden");
            }
            
            //check if form is ok
            if ($f->validate()) {
                //save message
                $nieuwresultaat = $field->new_sqlarray();
                foreach($f->get_values() as $key => $val) {
                    if ($key == "__formo")
                        continue;
                    $nieuwresultaat->$key = $val;
                }
                $nieuwresultaat->save();
                
                //now,  check whether we need to email the filled out form
                $formsettings = $field->get_sqlarray( array("where" => array("arrayname" => "formsettings") ) );
                if (is_object($formsettings)) {
                    if ($formsettings->component_forms_settings_emailopt == "tofixedaddress") {
                        //check if emailaddress is present and valid
                        if (!empty($formsettings->component_forms_settings_emailaddress) AND valid::email($formsettings->component_forms_settings_emailaddress)) {
                            //yes, email it!
                            $header = "From: email-formulier_" . $field->id . " <email-formulier_" . $field->id . "@" . $_SERVER["HTTP_HOST"] . ">\r\n"; //optional headerfields
                            //if it could have been known which field was used as contact emailaddress, we could add the "From" and "Reply-To" in a proper manner...
                            //however, as long as this cannot be known, we can also not add these headers appropriately
                            //$header .= "Reply-To: " .  <email-formulier_" . $field->id . "@" . $_SERVER["HTTP_HOST"] . ">\r\n"; //optional headerfields
                            $message = "";
                            foreach($nieuwresultaat as $key=>$val) {
                                $message .= $key . " : " . $val . "\r\n";
                            }
                            mail($formsettings->component_forms_settings_emailaddress, "Ingevuld formulier " . $field->id . " op " . $_SERVER["HTTP_HOST"], $message, $header);
                        }
                    }
                }
                
                
                //show message that result is saved
                $view->thankyoumessage = $thankyoumessage;
            } else {
                //display form along with errors
                $view->form = $f;
            }
            
            return $view->render(TRUE);
        }
        
        //is called when a user starts to edit a field
        function start_edit_field($field) {
            
            //load the current form as an sqlarray
            $form = $field->get_sqlarray( array("where" => array("arrayname" => "form")) );
            if (!is_object($form)) {
                $form = $field->new_sqlarray( array("arrayname" => "form") );
                $form->save();
            }
            
            //load the current form-settings as an sqlarray
            $formsettings = $field->get_sqlarray( array("where" => array("arrayname" => "formsettings")) );
            if (!is_object($formsettings)) {
                $formsettings = $field->new_sqlarray( array("arrayname" => "formsettings") );
                $formsettings->save();
            }
            
            //load thankyoumessage
            $thankyoumessage = $field->get("thankyoumessage");
            
            //load current results
            $results = $field->find_sqlarray();  //this will yield all related sqlarrays( thus the form results + the form itself)
            unset($results["form"]); //remove the form
            unset($results["formsettings"]);
            
            //load view and pass form and thankyoumessage to it, as well as current results
            $view = $this->view("forms_edit");
            $view->thankyoumessage = $thankyoumessage;
            $view->field = $field;
            $view->results = $results;
            $view->form = $form;
            $view->formsettings = $formsettings;
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => Array(
                    "fill" => Array("#wi3_editdiv_content" => $view->render())
                ),
                "scriptsafter" => Array("wi3_editdiv_show();", '$("#component_forms_edit_tabs").tabs();', "component_forms_attach_toggle();")
            )));
        }
        
        //is called when the user has stopped editing a field
        function stopped_edit_field($field) {
            
            //if text is changed, the cache should be reloaded
            Wi3::$cache->field_delete($field,"");
            
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
        
    }
    
?>
