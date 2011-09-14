<?php

    Class Component_forms_Controller extends Login_Controller {
        
        function __construct() {
            $this->template = "wi3/ajax";
            //make the Login Controller work!
            parent::__construct();
        }
        
        public function results($fieldid) {
            
            $field = ORM::factory("field", $fieldid);
            
            //load the current form as an sqlarray
            $form = $field->get_sqlarray( array("where" => array("arrayname" => "form")) );
            if (!is_object($form)) {
                $form = $field->new_sqlarray( array("arrayname" => "form") );
                $form->save();
            }
            
            //load current results
            $results = $field->find_sqlarray();  //this will yield all related sqlarrays( thus the form results + the form itself)
            unset($results["form"]); //remove the form
            unset($results["formsettings"]);
            
            ?>
            <table>
            <?php
                foreach($results as $datum => $result) {
                    echo "<tr><td>" . $datum . "</td><td></tr>";
                    foreach($result as $key => $val) {
                        echo "<tr><td></td><td>" . $key . "</td><td>" . $val . "</td></tr>";
                    }
                }
             ?>
        </table>
        <?
        }
        
        //---------------------------------------
        // AJAX functions
        //---------------------------------------
        public function savesettings($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            if (Wi3::$rights->check("edit", $field->page) == true) { //if one is allowed to edit the page of this field, it is allowed to move form-elements
            
                //fetch the formsettings-array
                $formsettings = $field->get_sqlarray( array("where" => array("arrayname" => "formsettings") ) );
                //create a new formsettings-array if it does not yet exist
                if (!is_object($formsettings)) {
                    $formsettings = $field->new_sqlarray( array("arrayname" => "formsettings") );
                }
                //set the objects
                foreach($_POST as $key => $val) {
                    $formsettings->$key = $val;
                }
                //save
                $formsettings->save();
                //return confirmation
                echo str_replace("\\n", "", json_encode(Array(
                    "alert" => "instellingen gewijzigd",
                    //"scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();")
                )));
            }
            
        }
            
        public function add($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            
            if (Wi3::$rights->check("edit", $field->page) == true) { //if one is allowed to edit the page of this field, it is allowed to add form-elements
            
                //add this type thing to the form
                $form = $field->get_sqlarray( array("where" => array("arrayname" => "form") ) );
                //create a new form-array if it does not yet exist
                if (!is_object($form)) {
                    $form = $field->new_sqlarray( array("arrayname" => "form") );
                    $form->save();
                }

                $name = $_POST["component_forms_addname"];
                //check if part already exists
                if (isset($form->_arrayparts[$name])) {
                    $exists = true;
                } else {
                    $exists = false;
                }
                
                $title = $_POST["component_forms_addtitle"];
                if (empty($title)) {
                    $title = $name;
                }
                $type = $_POST["component_forms_addtype"];
                $required = isset($_POST["component_forms_addrequired"]);
                
                $new = array("type" => $type , "name" => $name, "title" => $title, "required" => $required );
                //handle options, if they are set
                if (!empty($_POST["component_forms_addoptions"])) { 
                    $opts = explode(",", $_POST["component_forms_addoptions"]);
                    $optarray = array();
                    foreach($opts as $opt) {
                        $optarray[$opt] = $opt;
                    }
                    $new["options"] = $optarray;
                }
                $form->$name = serialize($new);
                $form->save();
                
                //reload form and  get ID of newly inserted part
                $form = $field->get_sqlarray( array("where" => array("arrayname" => "form") ) );
                $part = $form->_arrayparts[$name];
                $partdata = $new;
                
                $htmlwrapbegin ="<tr id='component_forms_part_" . $name . "'>";
                $htmlinner = "<td>" . $name . "</td><td><a href='javascript:void(0)' onClick='component_forms_edit(\"" . $name. "\");'>wijzigen</a></td><td><a href='javascript:void(0)' onClick='component_forms_moveup(this);'>naar boven</a></td><td><a href='javascript:void(0)' onClick='component_forms_movedown(this);'>naar onderen</a></td><td><a href='javascript:void(0)' onClick='component_forms_remove(\"" . $name .  "\");'><strong>verwijderen</strong></a></td>";
                $htmlinner .= "<td style='display: none;'>";
                    $htmlinner .= "<span id='component_forms_edit_info_type_" . $partdata["name"] .  "'>" . $partdata['type'] . "</span>";
                    $htmlinner .= "<span id='component_forms_edit_info_name_" . $partdata["name"] .  "'>" . $name . "</span>";
                    $htmlinner .= "<span id='component_forms_edit_info_title_" . $partdata["name"] .  "'>" . $partdata['title'] . "</span>";
                    $htmlinner .= "<span id='component_forms_edit_info_required_" . $partdata["name"] .  "'>" . ($partdata['required'] ? 1 : 0) . "</span>";
                    $htmlinner .= "<span id='component_forms_edit_info_options_" . $partdata["name"] .  "'>" . (isset($partdata['options']) ? $partdata['options'] : "") . "</span>";
                $htmlinner .= "</td>";
                $htmlwrapend = "</tr>";
                
                //if the part is new, then add it to the edit-form
                //if it is not new, replace the part already present on the form
                if ($exists) {
                    echo str_replace("\\n", "", json_encode(Array(
                        //"alert" => "nieuw element succesvol aangemaakt",
                        "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                        "dom" => array(
                            "fill" => array(
                                "#component_forms_part_" . $name => $htmlinner
                            )
                        )
                    )));
                } else {
                    echo str_replace("\\n", "", json_encode(Array(
                        //"alert" => "nieuw element succesvol aangemaakt",
                        "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                        "dom" => array(
                            "append" => array(
                                "#component_forms_existing" => $htmlwrapbegin . $htmlinner . $htmlwrapend
                            )
                        )
                    )));
                }
            }
        }
        
        public function remove($fieldid) {
            $field = ORM::factory("field", substr($fieldid, 10));
            if (Wi3::$rights->check("edit", $field->page) == true) { //if one is allowed to edit the page of this field, it is allowed to delete form-elements
                
                $name = $_POST["id"];
                
                //fetch the form
                $form = $field->get_sqlarray( array("where" => array("arrayname" => "form") ) );
                //create a new form-array if it does not yet exist
                if (!is_object($form)) {
                    $form = $field->new_sqlarray( array("arrayname" => "form") );
                }
                //now unset the element
                unset($form->$name);
                $form->save();
                
                echo str_replace("\\n", "", json_encode(Array(
                    //"alert" => "form succesvol aangepast",
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "dom" => array(
                        "remove" => array("#component_forms_part_" . $name)
                    )
                )));
            } else {
                echo str_replace("\\n", "", json_encode(Array(
                    "alert" => "u hebt niet het recht om dit form-element te verwijderen"
                )));
            }
        }
        
        public function move_up($fieldid) {
            $field = ORM::factory("field", substr($fieldid, 10));
            if (Wi3::$rights->check("edit", $field->page) == true) { //if one is allowed to edit the page of this field, it is allowed to move form-elements
                //simply swap the two parts
                $id1 = substr($_POST["base"], 21);
                $id2 = substr($_POST["to"], 21);
                $this->swap($field, $id1, $id2);
                echo str_replace("\\n", "", json_encode(Array(
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "scriptsafter" =>  array("var temp = $('#component_forms_part_" . $id1 . "').clone(); $('#component_forms_part_" . $id1 . "').remove(); $('#component_forms_part_" . $id2 . "').before(temp);")
                )));
            }
        }
        
        public function move_down($fieldid) {
            $field = ORM::factory("field", substr($fieldid, 10));
            if (Wi3::$rights->check("edit", $field->page) == true) { //if one is allowed to edit the page of this field, it is allowed to move form-elements
                //simply swap the two parts
                $id1 = substr($_POST["base"], 21);
                $id2 = substr($_POST["to"], 21);
                $this->swap($field, $id1, $id2);
                echo str_replace("\\n", "", json_encode(Array(
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "scriptsafter" =>  array("var temp = $('#component_forms_part_" . $id1 . "').clone(); $('#component_forms_part_" . $id1 . "').remove(); $('#component_forms_part_" . $id2 . "').after(temp);")
                )));
            }
        }
        
        //private function
        private function swap($field, $id1, $id2) {
            //fetch the form
            $form = $field->get_sqlarray( array("where" => array("arrayname" => "form") ) );
            //create a new form-array if it does not yet exist
            if (!is_object($form)) {
                $form = $field->new_sqlarray( array("arrayname" => "form") );
            }
            //now get the arrayparts
            $p1 = $form->_arrayparts[$id1];
            $p2 = $form->_arrayparts[$id2];
            $d1 = ORM::factory("sqlarraydata", $p1->id);
            $d2 = ORM::factory("sqlarraydata", $p2->id);
            $tempseqnr = $d1->seqnr;
            $d1->seqnr = $d2->seqnr;
            $d2->seqnr = $tempseqnr;
            $d1->save();
            $d2->save();
        }
        
        public function changethankyoumessage($fieldid) {
            $field = ORM::factory("field", substr($fieldid, 10));
            if (Wi3::$rights->check("edit", $field->page) == true) { //if one is allowed to edit the page of this field, it is allowed to edit form settings
                $field->set("thankyoumessage", $_POST["thankyoumessage"]);
                echo str_replace("\\n", "", json_encode(Array(
                    "alert" => "bericht succesvol aangepast",
                    //"dom" => array(
                    //    "fill" => array("#wi3_field_".$field->id, $this->render_field($field))
                    //)
                )));
            }
        }
        
    }

?>