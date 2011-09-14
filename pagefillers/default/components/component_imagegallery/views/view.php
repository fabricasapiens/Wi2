<?php

    global $firstimage;
    function render_images($images, $c, $field, $width, $breadcrumbs)
    {
        global $firstimage;
        echo "<div id='" . $c . "_" . $field->id . "_breadcrumbs'>" . $breadcrumbs . "</div>";
        if ($images and is_array($images))
        {
            //echo Kohana::debug($images);
            foreach($images as $id => $item) {
                // If an image, simply display. If it is a folder, then treat it as such
                if ($item->type == "file")
                {
                    echo "<div class='" . $c . "_thumbnail' id='" . $c . "_" . $field->id . "_" . $item->id . "_thumbnail' onClick='" . $c . "_viewimage({ src: \"" . htmlentities(Wi3::$urlof->image($item->filename, $width)) . "\", fullsrc: \"" . $item->url . "\", thumbid: \"" . $c . "_" . $field->id . "_" . $item->id . "_thumbnail\", fieldid: \"" . $field->id . "\"});'><img src='" . Wi3::$urlof->image($item->filename, 60) .  "'></img></div>";
                }
                else if ($item->type == "folder")
                {
                    // Only display the folder if it has children...
                    if (count($item->children) > 0)
                    {
                        echo "<div class='" . $c . "_folder' onclick='$(\"#" . $c . "_" . $field->id . "_thumbnails\").html($(\"#" . $c . "_" . $field->id . "_thumbnailfolder_" . $item->id . "\").html());'>";
                            echo "<div class='" . $c . "_foldername'><a href='javascript:void(0)'>" .  $item->filename . "</a></div>";
                            echo "<div style='display: none'>";
                                echo "<div id='" . $c . "_" . $field->id . "_thumbnailfolder_" . $item->id . "'>";
                                    render_images($item->children, $c, $field, $width, $breadcrumbs . " > <a style='border: none;' href='javascript:void(0);' onclick='$(\"#" . $c . "_" . $field->id . "_thumbnails\").html($(\"#" . $c . "_" . $field->id . "_thumbnailfolder_" . $item->id . "\").html());'>" . $item->filename . "</a>");
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    }
                }
            }
        }
        echo "<div class='" . $c . "_clearfix'>.</div>";
    }
    // The following structure holds all the different thumbnail-folders
    echo "<div style='display: none;'>";
        echo "<div id='" . $c . "_" . $field->id . "_thumbnailfolder_hoofdmap'>";
            render_images($images, $c, $field, $width, "<a style='border: none;' href='javascript:void(0);' onclick='$(\"#" . $c . "_" . $field->id . "_thumbnails\").html($(\"#" . $c . "_" . $field->id . "_thumbnailfolder_hoofdmap\").html());'>hoofdmap</a>");
        echo "</div>";
    echo "</div>";
    
    // The following div displays the active thumbnail-folder
    echo "<div id='" . $c . "_" . $field->id . "_thumbnails' class='" . $c . "_thumbnails'>";
        echo "<div id='" . $c . "_" . $field->id . "_breadcrumbs'><a style='border: none;' href='javascript:void(0);'>hoofdmap</a></div>";
        if ($images AND is_array($images))
        {
            foreach($images as $id => $item) {
                // If an image, simply display. If it is a folder, then treat it as such
                if ($item->type == "file")
                {
                    if (empty($firstimage)) { $firstimage = $item; }
                    echo "<div class='" . $c . "_thumbnail' id='" . $c . "_" . $field->id . "_" . $item->id . "_thumbnail' onClick='" . $c . "_viewimage({ src: \"" . htmlentities(Wi3::$urlof->image($item->filename, $width)) . "\", fullsrc: \"" . $item->url . "\", thumbid: \"" . $c . "_" . $field->id . "_" . $item->id . "_thumbnail\", fieldid: \"" . $field->id . "\"});'><img src='" . Wi3::$urlof->image($item->filename, 60) .  "'></img></div>";
                }
                else if ($item->type == "folder")
                {
                    // Only display the folder if it has children...
                    if (count($item->children) > 0)
                    {
                        echo "<div class='" . $c . "_folder' onclick='$(\"#" . $c . "_" . $field->id . "_thumbnails\").html($(\"#" . $c . "_" . $field->id . "_thumbnailfolder_" . $item->id . "\").html());'>";
                            echo "<div class='" . $c . "_foldername'><a href='javascript:void(0)'>" .  $item->filename . "</a></div>";
                        echo "</div>";
                    }
                }
            }
        }
        echo "<div class='" . $c . "_clearfix'>.</div>";
    echo "</div>";
    
    if (!isset($firstimage) OR !is_object($firstimage))
    {
        $firstimage = new StdClass();
        $firstimage->id = -1;
        $firstimage->url = "";
        $firstimage->filename = "";
    }
    
    
    echo "<div id='" . $c . "_" . $field->id . "_image' class='" . $c . "_image' ><div class='" . $c . "_imageinfo' style='width: " . $width . "px' ><a class='" . $c . "_fullimage' target='_blank' href='" . $firstimage->url . "'><span>bekijk volledige afbeelding</span></a><span style='display: none;' class='" . $c . "_currentthumbid'>" . $c . "_" . $field->id . "_" . $firstimage->id . "_thumbnail</span><span onClick='" . $c . "_previousimage(this);' class='" . $c . "_previous'>vorige</span><span onClick='" . $c . "_nextimage(this);' class='" . $c . "_next'>volgende</span></div><div><img src='" . Wi3::$urlof->image($firstimage->filename, $width) .  "'></img></div></div>";
    
    echo "<div class='" . $c . "_clearfix'>.</div>";

?>
