<?php

    class Component_univelektra_contactform_Controller extends Controller  {
        
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