<?php

    foreach($items as $id => $item) {
        if (valid::url($item->url)) {
            //check whether album title is known
            if (empty($item->title)) {
                $title = substr($item->url, (strrpos($item->url, "/")+1));
                $item->title = $title;
                $item->save();
            }
            //echo album title
            echo "<h2>" . $item->title . "</h2>";
            //check if Picasa image is already known 
            if (empty($item->albumimageurl)) {
                //fetch albumimage and save the location
                $html = file_get_contents($item->url);
                preg_match("@http://.*s144-c.*\.jpg@i", $html, $matches);
                if (!empty($matches)) {
                    $imageurl = $matches[0];
                    $item->albumimageurl = $imageurl;
                    $item->save();
                }
            }
            echo "<a target='_blank' href='" . $item->url . "'><img src='" . $item->albumimageurl . "'/></a>";
            echo $item->text;
            echo "<div class='component_picasaweblinks_floatfix'>.</div>";
        }
    }

?>