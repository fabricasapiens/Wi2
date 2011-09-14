<?php

    Class Pagefiller_default_field {
      
        //###FIELDS
        public function addFieldToDropzone($pageid, $dropzoneid, $fieldtype, $config = Array()) {
            if (is_numeric($pageid) AND valid::alpha_dash($dropzoneid)) {
                $page = ORM::factory("page", $pageid);
                $site = Wi3::$site;
                if ($page AND Wi3::$rights->check("edit", $page)) { //check whether user is allowed to do this
                    $falseroot = ORM::factory("field");
                    $falseroot->leftnr = 0;
                    $falseroot->rightnr = "999999999999999999999999";
                    $falseroot->scope = $page->id;
                    $imaginaryroot = $falseroot->get_tree($falseroot);
                    
                    $field = ORM::factory("field");
                    $field->page_id = $page->id;
                    //determine the type of field we want to edit
                    if (!isset($fieldtype) OR empty($fieldtype)) {
                        $fieldtype = "text";
                    }
                    $field->type = $fieldtype;
                    $field->scope = $page->id; //every page has only one tree of fields, so the tree-id is the same as the page-id
                    $field->site_id = $site->id;
                    
                    //#remove cache of the *complete* site!
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    //check whether the dropzone that we want to add the field in, exists
                    //and if not, create that dropzone
                    $dropzoneexists = false;
                    if (!empty($imaginaryroot->children)) { //if there are dropzones
                        foreach($imaginaryroot->children as $dropzone) {
                            if ($dropzone->type == $dropzoneid) { //'type' is used as a dropzone-id
                                $dropzoneexists = true;
                                break;
                            }
                        }
                    }
                    //create dropzone if it doesn't exist
                    if ($dropzoneexists == false) {
                        //create dropzone
                        $dropzone = ORM::factory("field");
                        $dropzone->page_id = $page->id;
                        $dropzone->scope = $page->id; //every page has only one tree of fields, so the tree-id is the same as the page-id
                        $dropzone->type = $dropzoneid;
                        $dropzone->site_id = $site->id;
                        //if there are no dropzones at all, that means there is no root yet... -> create one
                        if (empty($imaginaryroot->children)) {
                            $dropzone->make_root();
                        } else {
                            //insert as first child
                            $dropzone->insert_as_prev_sibling_of($dropzone->get_root()); //become first node
                        }
                    }
                    //and now insert the field into the dropzone!
                    if ($field->insert_as_first_child_of($dropzone)) {
                        //run event on the field, handing it the config that it initially should have
                        if (method_exists($field, "event")) {
                            $field->event("field_added", $config);
                        }
                        /*
                        echo json_encode(
                            Array(
                                "alert" =>"field with type " . $_POST["type"] . " has been created",
                                "scriptsafter" => Array("wi3_reload_page();")
                            )
                        );*/
                        return true;
                    }
                }
            }
            //if we get here, there's something wrong
            return false;
        }
        
        //-----------------------------------
        // functions to render a field, start editing a field and stop editing a field
        // these functions are most often called tby some AJAX request (through pagefiller_default_ajax controller)
        // pagefiller_default_ajax will respond with proper JSON messages to the user
        //-----------------------------------
        
        //render a certain field
        //@ return : some content in text-form
        function render($field) {
            //fetch field object, if it is not already one
            if (is_string($field)) {
                $field = ORM::factory("field", $field);
            }            
            if (empty($field->type)) {
                $field->type = "text";
            }
            $component = Pagefiller_default::factory("component", $field->type);
            return $component->render_field($field);
        }
        
        //is called when a user starts to edit a field
        //@ return : JSON for clientside
        function start_edit($field) {
            //fetch field object, if it is not already one
            if (is_string($field)) {
                $field = ORM::factory("field", $field);
            }
            if (empty($field->type)) {
                $field->type = "text";
            }
            $component = Pagefiller_default::factory("component", $field->type);
            return $component->start_edit_field($field);
        }
        
        //is called when the user has stopped editing a field
        //@ return : JSON for clientside
        function stopped_edit($field) {
            //fetch field object, if it is not already one
            if (is_string($field)) {
                $field = ORM::factory("field", $field);
            }
            if (empty($field->type)) {
                $field->type = "text";
            }
            $component = Pagefiller_default::factory("component", $field->type);
            return $component->stopped_edit_field($field);
            
        }
        
        //delete a certain field
        //@ return : JSON for clientside
        function delete($field) {
            //fetch field object, if it is not already one
            if (is_string($field)) {
                $field = ORM::factory("field", $field);
            }
            if (empty($field->type)) {
                $field->type = "text";
            }
            $component = Pagefiller_default::factory("component", $field->type);
            try { //function does not have to exist
                $a = $component->delete_field($field);
                return $a;
            } catch(Exception $e) {
                return;
            }
            
        }
        
    }

?>