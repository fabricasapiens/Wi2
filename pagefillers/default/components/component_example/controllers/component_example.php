<?php

    //TODO caching!

    Class Component_example_Controller extends Login_Controller {
        
        function __construct() {
            //set the template to use an empty one
            $this->template = "wi3/ajax";
            //make the Login Controller work!
            parent::__construct();
        }
        
        public function items_view_all($fieldid) {
            //one can only view all the items if one is allowed to edit the containing page
            $field = ORM::factory("field", $fieldid);
            if (Wi3::$rights->check("edit", $field->page)) {
                $items = $field->find_sqlarray( array("where" => array("groupname" => "items")) );
                return $items;
            }
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
            
            echo "<table>";
                foreach($results as $datum => $result) {
                    echo "<tr><td>" . $datum . "</td><td></tr>";
                    foreach($result as $key => $val) {
                        echo "<tr><td></td><td>" . $key . "</td><td>" . $val . "</td></tr>";
                    }
                }
            echo "</table>";
        }
        
        //---------------------------------------
        // AJAX functions
        //---------------------------------------
        public function add($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            //one can only add this item to this field, if one can edit the containing page
            if (Wi3::$rights->check("edit", $field->page)) {
                
                //create new item
                $item = $field->new_sqlarray();
                foreach($_POST as $key => $val) {
                    $item->$key = $val;
                }
                $item->setgroup("items"); //assign this array to the 'items' group (this group still being under the $field)
                $item->save();
                // --------------------------------------------------
                //slug creation
                //can come in handy when creating URLs pointing to a certain item
                //we here use the ->name to create the slug for an item. One could also use the unique arrayname, which is generated when saving an sqlarray
                // --------------------------------------------------
                if (isset($item->name)) {
                    $slug= url::title($item->name);
                    //check if this slug does not already exist
                    $counter = 0;
                    while($field->get_sqlarray( array("where" => array(
                    "arraygroup" => "items",
                    "slug" => $slug
                    ))) != null) {
                        $counter++;
                        $slug= url::title($item->name . "-" . $counter);
                    }
                } else {
                    //if there's no ->name, we create the slug from the arrayname
                    //that would be a unique numeric ID, so no need to check for existance or cleaning
                    $slug = $item->_arrayname;
                }
                $item->slug = $slug;
                $item->save();
                //end of slug creation
                
                $prefix = $field->type;
                
                $id = $field->id;
                $htmlwrapbegin = "<tr id='" . $prefix . "_item_" . $id . "'>";
                $htmlinner = "<td>" . $item->name . "</td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".edit(\"" . $id . "\");'>wijzigen</a></td><td><a href='javascript:void(0)' onClick='component_forms_moveup(this);'>naar boven</a></td><td><a href='javascript:void(0)' onClick='component_forms_movedown(this);'>naar onderen</a></td><td><a href='javascript:void(0)' onClick='component_forms_remove(\"" . $id .  "\");'><strong>verwijderen</strong></a></td>";
                //expose information of the elements, so that they can be edited
                $htmlinner .= "<td style='display: none;'>";
                    //iterate over the array and render its info
                    $localprefix = $prefix . "_field_" . $id . "_";
                    foreach($item as $key => $val) {
                        $htmlinner .= "<span id='" . $localprefix . $key . "' name='" . $key . "'>" . $val . "</span>";
                    }
                $htmlinner .= "</td>";
                $htmlwrapend = "</tr>";
                
                echo str_replace("\\n", "", json_encode(Array(
                    //"alert" => "nieuw element succesvol aangemaakt",
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "dom" => array(
                        "append" => array(
                            "#" . $prefix . "_existing" => $htmlwrapbegin . $htmlinner . $htmlwrapend
                        )
                    )
                )));
            } else {
                echo "geen rechten";
            }
        }
        
        public function edit($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            //one can only edit this field, if one can edit the containing page
            if (Wi3::$rights->check("edit", $field->page)) {
                
                //edit item (find it from its ID)
                $arrayname = substr($_POST["__id"], 5);
                $item = $field->get_sqlarray(array("where" => array("arrayname" => $arrayname)));
                unset($_POST["__id"]);
                foreach($_POST as $key => $val) {
                    $item->$key = $val;
                }
                $item->save();
                // --------------------------------------------------
                //slug creation
                //can come in handy when creating URLs pointing to a certain item
                //we here use the ->name to create the slug for an item. One could also use the unique arrayname, which is generated when saving an sqlarray
                // --------------------------------------------------
                if (isset($item->name)) {
                    $slug= url::title($item->name);
                    //check if this slug does not already exist
                    $counter = 0;
                    $existingitem = $field->get_sqlarray( array("where" => array(
                    "arraygroup" => "items",
                    "slug" => $slug
                    )));
                    //when there is an existing item with the very same slug (and it is not this item)
                    //create a new slug and try again
                    while($existingitem != null AND $existingitem->_arrayname != $item->_arrayname) {
                        $counter++;
                        $slug= url::title($item->name . "-" . $counter);
                        $existingitem = $field->get_sqlarray( array("where" => array(
                        "arraygroup" => "items",
                        "slug" => $slug
                        )));
                    }
                } else {
                    //if there's no ->name, we create the slug from the arrayname
                    //that would be a unique numeric ID, so no need to check for existance or cleaning
                    $slug = $item->_arrayname;
                }
                $item->slug = $slug;
                $item->save();
                //end of slug creation
                
                $prefix = $field->type;
                
                $id = $item->_arrayname;
                //expose information of the elements, so that they can be edited
                //iterate over the array and render its info
                $localprefix = $prefix . "_field_" . $id . "_";
                $htmlinner = "";
                foreach($item as $key => $val) {
                    $htmlinner .= "<span id='" . $localprefix . $key . "' name='" . $key . "'>" . $val . "</span>";
                }
                
                echo str_replace("\\n", "", json_encode(Array(
                    //"alert" => "nieuw element succesvol aangemaakt",
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "dom" => array("fill" => array(
                        "#" . $prefix . "_item_" . $id . " td:last" => $htmlinner
                    ))
                )));
            } else {
                echo "geen rechten";
            }
        }
        
        public function remove($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            //one can only edit this field, if one can edit the containing page
            if (Wi3::$rights->check("edit", $field->page)) {
                
                //edit item (find it from its ID)
                $arrayname = $_POST["id"];
                $item = $field->get_sqlarray(array("where" => array("arrayname" => $arrayname)));
                //delete it
                $item->delete();
                
                $prefix = $field->type;
                
                echo str_replace("\\n", "", json_encode(Array(
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "dom" => Array(
                        "remove" => Array("#" . $prefix . "_item_" . $arrayname)
                    )
                )));
            } else {
                echo "geen rechten";
            }
        }
        
        public function moveup($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            //one can only edit this field, if one can edit the containing page
            if (Wi3::$rights->check("edit", $field->page)) {
                
                $prefix = $field->type;
                
                //swap two arrays/items
                $swapbase = substr($_POST["swapbase"], strlen($prefix)+6);
                $swapwith = substr($_POST["swapwith"], strlen($prefix)+6);
                $item1 = $field->get_sqlarray(array("where" => array("arrayname" => $swapbase)));
                $item2 = $field->get_sqlarray(array("where" => array("arrayname" => $swapwith)));
                $item1seqnr = $item1->_arrayseqnr;
                $item1->setseqnr($item2->_arrayseqnr);
                $item2->setseqnr($item1seqnr);
                //save
                $item1->save();
                $item2->save();
                
                echo str_replace("\\n", "", json_encode(Array(
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "scriptsafter" =>  array("var temp = $('#" . $_POST["swapbase"] . "').clone(); $('#" . $_POST["swapbase"] . "').remove(); $('#" . $_POST["swapwith"] . "').before(temp);")
                )));
            } else {
                echo "geen rechten";
            }
        }
        
        public function movedown($fieldid) {
            
            $field = ORM::factory("field", substr($fieldid, 10));
            //one can only edit this field, if one can edit the containing page
            if (Wi3::$rights->check("edit", $field->page)) {
                
                $prefix = $field->type;
                
                //swap two arrays/items
                $swapbase = substr($_POST["swapbase"], strlen($prefix)+6);
                $swapwith = substr($_POST["swapwith"], strlen($prefix)+6);
                $item1 = $field->get_sqlarray(array("where" => array("arrayname" => $swapbase)));
                $item2 = $field->get_sqlarray(array("where" => array("arrayname" => $swapwith)));
                $item1seqnr = $item1->_arrayseqnr;
                $item1->setseqnr($item2->_arrayseqnr);
                $item2->setseqnr($item1seqnr);
                //save
                $item1->save();
                $item2->save();
                
                echo str_replace("\\n", "", json_encode(Array(
                    "scriptsbefore" => array("wi3.pagefiller.editing.reloadCurrentField();"),
                    "scriptsafter" =>  array("var temp = $('#" . $_POST["swapbase"] . "').clone(); $('#" . $_POST["swapbase"] . "').remove(); $('#" . $_POST["swapwith"] . "').after(temp);")
                )));
            } else {
                echo "geen rechten";
            }
        }
        
    }

?>