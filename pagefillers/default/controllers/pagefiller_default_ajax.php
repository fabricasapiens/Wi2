<?php

    class Pagefiller_default_ajax_Controller extends Login_Controller {
        
        public $template = "wi3/ajax";
        
        public function __construct() {
            parent::__construct();
            //run the siteandpageloaded event, so that the Pathof en Urlof will be executed
            //Event::run("wi3.siteandpageloaded");
        }
        
        //----------------------------------------
        // basic function to create a field or sitefield, based on a certain fieldid
        //----------------------------------------
        public function fieldfromid($fieldid) {
            if (empty($fieldid) OR stripos($fieldid, "field") === false) {
                return false;
            }
            if (strpos($fieldid, "siteField") > 0) {
                //a siteField
                $id = substr($fieldid, 14);
                return ORM::factory("siteField", $id);
            } else {
                //a normal field
                $id = substr($fieldid, 10);
                return ORM::factory("field", $id);
            }
        }
        
        //----------------------------------------
        // functions that handle all kind of ajax requests
        //----------------------------------------
        public function startEditField($fieldid = "") {
            $field= $this->fieldfromid($fieldid);
            //check whether we are working with a siteField or a normal field
            if (get_class($field) == "Field_Model") {
                //get page we are working on
                Wi3::$page = $field->page;
                //run the siteandpageloaded event, so that the Pathof en Urlof will be properly filled
                Event::run("wi3.siteandpageloaded");
            }
            new Pagefiller_default(); //calls __construct which enables the Pagefiller::$field
            return Pagefiller_default::$field->start_edit($field);
        }
        
         public function stoppedEditField($fieldid = "") {
            $field= $this->fieldfromid($fieldid);
            new Pagefiller_default(); //calls __construct which enables the Pagefiller::$field
            return Pagefiller_default::$field->stopped_edit($field);
        }
        
        function deleteField($fieldid = "") {
            $field= $this->fieldfromid($fieldid);
            if (!is_numeric($field->id) OR $field->id == 0) {
                //is already deleted
                echo json_encode(
                    Array(
                        "alert" =>"veld is verwijderd.",
                        "dom" => Array(
                            "remove" => Array("#".$fieldname)
                        )
                    )
                );
            }
            //only delete field if it belongs to a page that the user is allowed to edit
            if (Wi3::$rights->check("edit", $field->page)) {
                $pagefiller = new Pagefiller_default();
                $pagefiller->deleteField($field);
                //remove cache of the *complete* site!
                Wi3::cache_field_delete_all_wheresite($site);
                //return
                echo json_encode(
                    Array(
                        "alert" =>"veld is verwijderd.",
                        "dom" => Array(
                            "remove" => Array("#".$fieldid)
                        )
                    )
                );
            }
        }
        
        public function reloadField($fieldormid) {
            $field = ORM::factory("field", $fieldormid);
            if (Wi3::$rights->check("edit", $field->page)) {
                //now first set the page we are working with
                //the field might need it with rendering (assuming a correct Wi3::$page or when
                //the component needs it when asking for the Wi3::$pathof->pagefiller)
                Wi3::$page = $field->page;
                Event::run("wi3.siteandpageloaded");
                $fieldcontent = $field->render();
            } else {
                $fieldcontent = "U hebt niet het recht acties uit te voeren op dit veld.";
            }
            echo json_encode(
                Array(
                    "dom" => Array(
                        "fill_withfade" => Array("#wi3_field_".$fieldormid => $fieldcontent)
                    )
                )
            );
        }
        
    }
    

?>