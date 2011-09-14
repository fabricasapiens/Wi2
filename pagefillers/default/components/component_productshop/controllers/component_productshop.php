<?php

    //TODO caching!

    Class Component_Productshop_Controller extends Loginifpossible_Controller {
        
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
                $items = $field->find_sqlarray( array("where" => array("arraygroup" => "items")) );
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
        
        // -----------------
        // Public AJAX functions
        // -----------------
        public function addtocart()
        {
            $session = Session::instance();
            $productid = $_POST["productid"];
            $amount = $_POST["amount"];
            $fieldnr = $_POST["fieldnr"];
            
            $session->set("component_productshop_fieldnr", $fieldnr);
            
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            if (isset($productsincart[$productid]))
            {
                $productsincart[$productid] += $amount;
            } 
            else
            {
                $productsincart[$productid] = $amount;
            }
            $session->set("component_productshop_productsincart", serialize($productsincart));
            
            // Get HTML for the upper-left cart button
            $html = "<div id='component_productshop_cartimage'></div>";
            $totalamount = 0;
            foreach($productsincart as $id => $amount)
            {
                $totalamount += $amount;
            }
            $html .= "<p>Winkelwagen<br /><strong> (" . $totalamount . " producten)</strong> bekijken en bestellen.</p>";
            
            echo str_replace("\\n", "", json_encode(Array(
                "alert" => "Product is in winkelwagen geplaatst!\r\n\r\nKlik op de winkelwagen (linksboven) om de bestelling af te ronden.",
                "dom" => array(
                    "fill" => array(
                        "#component_productshop_cart" => $html
                    )
                )
            )));
        }
        
        public function removefromcart()
        {
            $session = Session::instance();
            $productid = $_POST["productid"];
            
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            if (isset($productsincart[$productid]))
            {
                unset($productsincart[$productid]);
            }
            $session->set("component_productshop_productsincart", serialize($productsincart));
            
            // Get HTML for the upper-left cart button
            $html = "<div id='component_productshop_cartimage'></div>";
            $totalamount = 0;
            foreach($productsincart as $id => $amount)
            {
                $totalamount += $amount;
            }
            $html .= "<p>Winkelwagen<br /><strong> (" . $totalamount . " producten)</strong> bekijken en bestellen.</p>";
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => array(
                    "fill" => array(
                        "#component_productshop_cart" => $html
                    ),
                    "remove" => array(
                        "#component_productshop_cart_overview_product_".$productid
                    )
                )
            )));
        }
        
        public function removeonefromcart()
        {
            $session = Session::instance();
            $productid = $_POST["productid"];
            
            $remove = array();
            
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            $productamount = 0;
            if (isset($productsincart[$productid]))
            {
                $productsincart[$productid]--;
                $productamount = $productsincart[$productid];
                if ($productsincart[$productid] == 0) { 
                    unset($productsincart[$productid]); 
                    $remove = array(
                        "#component_productshop_cart_overview_product_".$productid
                    );
                }
            }
            $session->set("component_productshop_productsincart", serialize($productsincart));
            
            // Get HTML for the upper-left cart button
            $html = "<div id='component_productshop_cartimage'></div>";
            $totalamount = 0;
            foreach($productsincart as $id => $amount)
            {
                $totalamount += $amount;
            }
            $html .= "<p>Winkelwagen<br /><strong> (" . $totalamount . " producten)</strong> bekijken en bestellen.</p>";
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => array(
                    "fill" => array(
                        "#component_productshop_cart" => $html,
                        "#component_productshop_cart_overview_product_".$productid." td:nth-child(2)" => $productamount
                    ),
                    "remove" => $remove
                )
            )));
        }
        
        public function addonetocart()
        {
            $session = Session::instance();
            $productid = $_POST["productid"];
            
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            $productamount = 0;
            if (isset($productsincart[$productid]))
            {
                $productsincart[$productid]++;
                $productamount = $productsincart[$productid];
            }
            $session->set("component_productshop_productsincart", serialize($productsincart));
            
            // Get HTML for the upper-left cart button
            $html = "<div id='component_productshop_cartimage'></div>";
            $totalamount = 0;
            foreach($productsincart as $id => $amount)
            {
                $totalamount += $amount;
            }
            $html .= "<p>Winkelwagen<br /><strong> (" . $totalamount . " producten)</strong> bekijken en bestellen.</p>";
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => array(
                    "fill" => array(
                        "#component_productshop_cart" => $html,
                        "#component_productshop_cart_overview_product_".$productid." td:nth-child(2)" => $productamount
                    )
                )
            )));
        }
        
         public function loadcartoverview()
        {
            $session = Session::instance();
            
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            $productshopfieldnr = $session->get("component_productshop_fieldnr");
            if (empty($productshopfieldnr))
            {
                echo str_replace("\\n", "", json_encode(Array(
                    "dom" => array(
                        "fill" => array(
                            "#component_productshop_cart_orderform" => "<div style='text-align: center;'>Er zijn nog geen producten in de winkelmand geplaatst.</div>"
                        )
                    )
                )));
                return;
            }
            $result = "<h2>Winkelmand</h2>";
            $result .= "<table>";
            $result .= "<tr><td style='padding-bottom: 5px;'><strong>product</strong></td><td><strong>aantal</strong></td><td><strong>+</strong></td><td><strong>-</strong></td><td><strong>verwijderen</strong></td></tr>";
            // Get all the possible items for this field
            $field = ORM::factory("field", $productshopfieldnr);
            $items = $field->find_sqlarray( array("where" => array("arraygroup" => "items")) );
            $name = "";
            foreach($productsincart as $id => $amount)
            {
                // Now get the proper name with the ID
                foreach($items as $itemid => $item)
                {
                    if ($itemid == $id)
                    {
                        $name = $item->name;
                        break;
                    }
                }
                $result .= "<tr id='component_productshop_cart_overview_product_".$id."'><td>" . $name . "</td><td>" . $amount . "</td><td><button onClick='wi3.pagefiller.components.component_productshop.addonetocart(\"" . $id . "\");'>+</button></td><td><button onClick='wi3.pagefiller.components.component_productshop.removeonefromcart(\"" . $id . "\");'>-</button></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components.component_productshop.removefromcart(\"" . $id . "\");'>verwijderen</a></td></tr>";
            }
            $result .= "</table>";
            $result .= "<div style='height: 10px; width: 100%;'></div>";
            $result .= "<h2>Bestelformulier</h2>";
            $result .= "<div>";
                $result .= "<p>vul onderstaand formulier in om te bestellen</p>";
                $result .= "<div style='height: 10px; width: 100%;'></div>";
                $result .= "<form id='component_productshop_cart_orderform_form' onSubmit='return false;'>";
                $result .= "<div>naam <input name='name'/></div>";
                $result .= "<div>emailadres <input name='email'/></div>";
                $result .= "<div>straat+nr <input name='street'/></div>";
                $result .= "<div>postcode <input name='zipcode'/></div>";
                $result .= "<div>stad <input name='city'/></span></div>";
                $result .= "</form>";
                $result .= "<button onClick='wi3.pagefiller.components.component_productshop.order($(this).prev());'>bestelling plaatsen</button>";
            $result .= "</div>";
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => array(
                    "fill" => array(
                        "#component_productshop_cart_orderform" => $result
                    )
                )
            )));
        }
        
        // Order
         public function order()
        {
            $session = Session::instance();
            
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            $productshopfieldnr = $session->get("component_productshop_fieldnr");
            
            // Get all the possible items for this field
            $field = ORM::factory("field", $productshopfieldnr);
            $items = $field->find_sqlarray( array("where" => array("arraygroup" => "items")) );
            $name = "";
            $mail = "Er zijn producten besteld via de webshop:\r\n\r\n";
            $counter = 0;
            foreach($productsincart as $id => $amount)
            {
                $counter++;
                // Now get the proper name with the ID
                foreach($items as $itemid => $item)
                {
                    if ($itemid == $id)
                    {
                        $name = $item->name;
                        break;
                    }
                }
                $mail .= $counter . ". Product " . $name . " (id: " . $id . "), is "  . $amount . " keer besteld.\r\n";
            }
            $mail .= "\r\nKlantgegevens:\r\n\r\n";
            foreach($_POST as $key => $val)
            {
                $mail .= $key . " : " . $val . "\r\n";
            }
            if (mail( "info@univelektra.nl", "Bestelling van webshop " . $_SERVER['SERVER_NAME'], $mail))
            {
                // Get HTML
                $html = "<p>De bestelling is succesvol verzonden! Er wordt zo spoedig mogelijk contact met u opgenomen voor verdere verwerking van de bestelling.</p><p>Hartelijk dank voor uw bestelling via " . $_SERVER["SERVER_NAME"] . ".</p>";
                
                echo str_replace("\\n", "", json_encode(Array(
                    "dom" => array(
                        "fill" => array(
                            "#component_productshop_cart_orderform" => $html,
                        )
                    )
                )));
            }
            else
            {
                 echo str_replace("\\n", "", json_encode(Array(
                    "alert" => "Er ging iets fout bij het bestellen! Zijn alle gegevens correct ingevoerd? Als de fout blijft aanhouden, neem dan contact op via het contactformulier."
                )));
            }
        }
    
    } // End of controller

?>