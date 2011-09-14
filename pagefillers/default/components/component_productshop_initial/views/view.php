<?php

    echo "<div class='" . $c . "_thumbnails'>";
        $firstimage = "";
        foreach($images as $id => $item) {
            if (empty($firstimage)) { $firstimage = $item; }
            echo "<div class='" . $c . "_thumbnail' id='" . $c . "_" . $field->id . "_" . $item->id . "_thumbnail' onClick='" . $c . "_viewimage({ src: \"" . htmlentities(Wi3::$urlof->image($item->filename, $width)) . "\", fullsrc: \"" . $item->url . "\", thumbid: \"" . $c . "_" . $field->id . "_" . $item->id . "_thumbnail\", fieldid: \"" . $field->id . "\"});'><img src='" . Wi3::$urlof->image($item->filename, 60) .  "'></img></div>";
        }
        echo "<div class='" . $c . "_clearfix'>.</div>";
    echo "</div>";
    
    echo "<div id='" . $c . "_" . $field->id . "_image' class='" . $c . "_image' ><div class='" . $c . "_imageinfo' style='width: " . $width . "px' ><a class='" . $c . "_fullimage' target='_blank' href='" . $firstimage->url . "'><span>bekijk volledige afbeelding</span></a><span style='display: none;' class='" . $c . "_currentthumbid'>" . $c . "_" . $field->id . "_" . $firstimage->id . "_thumbnail</span><span onClick='" . $c . "_previousimage(this);' class='" . $c . "_previous'>vorige</span><span onClick='" . $c . "_nextimage(this);' class='" . $c . "_next'>volgende</span></div><div><img src='" . Wi3::$urlof->image($firstimage->filename, $width) .  "'></img></div></div>";
    
    echo "<div class='" . $c . "_clearfix'>.</div>";

?>