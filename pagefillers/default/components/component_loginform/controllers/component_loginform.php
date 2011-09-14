<?php

    Class Component_loginform_Controller extends Login_Controller {
        
        function __construct() {
            $this->template = "wi3/ajax";
        }
        
        //---------------------------------------
        // AJAX functions
        //---------------------------------------
        
        public function changethankyoumessage($fieldid) {
            $field = ORM::factory("field", substr($fieldid, 10));
            $field->set("thankyoumessage", $_POST["thankyoumessage"]);
            
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "bericht succesvol aangepast",
                //"dom" => array(
                //    "fill" => array("#wi3_field_".$field->id, $this->render_field($field))
                //)
            )));
        }
        
        public function changeerrormessage($fieldid) {
            $field = ORM::factory("field", substr($fieldid, 10));
            $field->set("errormessage", $_POST["errormessage"]);
            
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "bericht succesvol aangepast",
                //"dom" => array(
                //    "fill" => array("#wi3_field_".$field->id, $this->render_field($field))
                //)
            )));
        }
        
    }

?>