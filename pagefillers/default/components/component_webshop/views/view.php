<?php

    echo "<div class='component_webshop_items'>";
    foreach($items as $id => $item) {
        echo "<div class='component_webshop_item'>";
            echo "<img src='" . Wi3::$urlof->image($item->productImage, 70) .  "'/>";
            echo "<div class='component_webshop_text'>";
                echo "<h2>" . $item->name . "</h2>";
                echo "<div>" . $item->price . " euro</div>";
                echo "<div class='component_webshop_addtocart'><img src='" . Wi3::$urlof->pagefiller . "components/component_webshop/static/images/shoppingcart.png'/><button onClick='wi3.pagefiller.components.component_webshop.addtocart(\"article_" . $item->_arrayname . "\");'>bestellen</button></div>";
                echo "<div class='component_webshop_item_additional'>";
                    echo "<h3>details:</h3>";
                    echo "<div>" . $item->description . "</div>";
                    echo "<h3>nummers:</h3>";
                    //now show all the tracks, with their 'preview-track'
                    foreach($item as $key => $value) {
                        if (!empty($value) AND substr($key, 0, 6) == "track_" AND substr($key, -6, 6) == "_title") {
                            echo "" . substr($key,6,strlen($key)-12) . ". <strong>" . $value . "</strong>";
                            $linkedfile = "track_" . substr($key,6,strlen($key)-12) . "_linkedfile";
                            if (isset($item->$linkedfile) AND !empty($item->$linkedfile) AND $item->$linkedfile != "nofile") {
                                echo " (<a target='_blank' href='" . Wi3::$urlof->file($item->$linkedfile) . "'>voorbeeld</a>)";
                            }
                            echo "<br />";
                        }
                    }
                echo "</div>";
            echo "</div>";
            echo "<div class='component_webshop_clearfix'></div>";
        echo "</div>"; //end item
    }
    echo "</div>"; //end items
    
    //now echo the order-form
    echo "<div class='component_webshop_orderformcontainer'>";
        echo "<div class='component_webshop_orderform'>";
            ?>
            <h1>Bestelformulier</h1>
            <?
            $f = Formo::factory();
            $f->plugin('auto_i18n')->plugin('csrf');
            $f->add("html", "persoonlijk", "<h3>persoonlijke gegevens</h3><br/>");
            $f->add("text", "name", array("label" => "naam"))->required(true);
            $f->add("text", "street", array("label" => "straat + nr"));
            $f->add("text", "postal code", array("label" => "postcode"));
            $f->add("text", "city", array("label" => "woonplaats"));
            $f->add("text", "telephone", array("label" => "tel. nr"));
            $f->add("text", "email", array("label" => "emailadres"))->required(true);
            $f->add("html", "prijzen", "<h3>welke artikelen wilt u bestellen?</h3><br/>");
            foreach($items as $item) {
                $f->add("select", "article_" . $item->_arrayname, array("values" => Array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20"), "label"=> "CD \"" . $item->name . "\""));
            }
            $f->add("submit","submit","verzenden");
            //$f->add($array["type"], $array["name"], $array["title"]); //html of submit
            //$f->add($array["type"], $array["name"], array("label" => $array["title"])); //rest
            
            //check if form was sent correctly
            if ($f->validate()) {
                //save message
                /*
                foreach($f->get_values() as $key => $val) {
                    if ($key == "__formo")
                        continue;
                    $nieuwresultaat->$key = $val;
                }
                $nieuwresultaat->save();&*/
                //send email message
                $header = "From: webshop " .  $_SERVER["HTTP_HOST"] . "<webshop@" . $_SERVER["HTTP_HOST"] . ">\r\n"; //optional headerfields
                $header .= "Reply-To: " . strip_tags($_POST["name"]) . " <" . $_POST["email"] . ">\r\n"; //optional headerfields
                $message = "";
                foreach($f->get_values() as $key=>$val) {
                    if (strpos($key, "article_") === 0) {
                        $nr = substr($key, 8);
                        $it = $items[$nr];
                        $key = $it->name;
                    }
                    $message .= $key . " : " . $val . "\r\n";
                }
                
                //fetch the formsettings-array
                $settings = $field->get_sqlarray( array("where" => array("arrayname" => "settings") ) );
                if (!is_object($settings)) {
                    $settings = $field->new_sqlarray( array("arrayname" => "settings") );
                }
                if (!empty($settings->email)) {
                    mail($settings->email, "Ingevuld formulier " . $field->id . " op " . $_SERVER["HTTP_HOST"], $message, $header);
                    echo "<p>uw bestelling is succesvol verzonden! Er wordt zo spoedig mogelijk contact met u opgenomen.</p>";
                } else {
                    echo "<p>er ging helaas iets <strong>fout met het verzenden</strong> van uw bestelling. Probeer het nog eens, of stuur een mail naar info@laudate-deum.nl.</p>";
                }
                echo "<div style='display: none;' class='component_webshop_orderform_showit'></div>";
                
            } else {
                if (!empty($_POST)) {
                    //there was something wrong
                    //render the form in this hidden div and then add the 'show' div that will make sure the div is shown on screen
                    echo "<div style='display: none;' class='component_webshop_orderform_showit'></div>";
                    echo $f;
                } else {
                    //just render the form
                    echo $f;
                }
            }
            
            /*<form id='<?php echo $prefix; ?>_order' onSubmit='return wi3.pagefiller.components.<?php echo $prefix; ?>.order();'><table>*/
        echo "</div>";
    echo "</div>";

?>