<?php

    // The cart box
    echo "<div style='float: left; height: 150px; width: 112px; margin: 5px;'>";
        echo "<div id='component_productshop_cart'>";
            echo "<div id='component_productshop_cartimage'></div>";
            $session = Session::instance();
            $productsincart = unserialize($session->get("component_productshop_productsincart", serialize(array())));
            $totalamount = 0;
            foreach($productsincart as $id => $amount)
            {
                $totalamount += $amount;
            }
            if ($totalamount == 0)
            {
                echo "<p>Klik op een product om het in de winkelwagen te plaatsen.</p>";
            }
            else 
            {
                echo "<p>Winkelwagen<br /><strong> (" . $totalamount . " producten)</strong> bekijken en bestellen.</p>";
            }
        echo "</div>";
    echo "</div>";
    
    

    foreach($items as $id => $item) {
        echo "<div style='float: left; height: 150px; margin: 5px;'>";
            echo "<div style='width: 100px; overflow: hidden; border: 1px solid #ddd; padding: 5px;'>";
                // The 'title' will appear in the fancybox overlay, under the actual product image
                // This title includes a little form to add to the shopping cart
                $title = '<div><select product="' . $id . '" fieldnr="' . $field->id . '">';
                for($i=1;$i<20;$i++)
                {
                    $title .= "<option value=\"" . $i . "\">" . $i . "</option>";
                }
                $title .= "</select> ";
                $title .= '<button onClick="wi3.pagefiller.components.component_productshop.addtocart($(this).prev());">toevoegen aan winkelwagen</button></div>';
                $title .= "<div>" . $item->name.": ".$item->text . "</div>";
                echo"<a style='position: block;' title='".$title."' class='pagefiller_default_component_productshop_gallery' rel='products' href='".Wi3::$urlof->image($item->image, 400)."'>";
                    echo "<img src='" . Wi3::$urlof->image($item->image, 100) .  "'/>";
                echo"</a>";
                echo "<div style='text-align: center;'>" . $item->name . "</div>";
                if (empty($item->price_eurocent)) { $item->price_eurocent = "00"; }
                if (strlen($item->price_eurocent) == 1) { $item->price_eurocent = "0" . $item->price_eurocent; }
                if (empty($item->price_euro)) { $item->price_euro = "0"; }
                echo "<div style='text-align: center;'><small>prijs: " . $item->price_euro . "," . $item->price_eurocent . "</small></div>";
            echo"</div>";
        echo "</div>";
    }
    
    echo "<div style='clear:both;'></div>";
    
    echo '<script>$(document).ready(function() { $(".pagefiller_default_component_productshop_gallery").fancybox({"titlePosition" : "inside"}); });</script>';

?>