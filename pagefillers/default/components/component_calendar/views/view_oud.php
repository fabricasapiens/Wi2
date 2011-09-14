<?php

    if (isset($item) AND !isset($items)) {
        //display one certain article, not all 
        echo "<div>";
            //date is most important
            echo "<h2>" . $item->date . " - " . $item->title . "</h2>";
            echo "<div>";
                echo $item->fullText;
                echo "<div class='component_calendar_floatfix'>.</div>";
            echo "</div>";
            echo "<a href='" . Wi3::$urlof->page(Wi3::$page->title) . "'>terug...</a>";
        echo "</div>";
    } else if (isset($items)) {
        //todo: pagination
        foreach($items as $id => $item) {
            echo "<div>";
                //date is most important
                echo "<h2>" . $item->date . " - " . $item->title . "</h2>";
                echo "<div>";
                    echo $item->summary;
                    echo "<div class='component_calendar_floatfix'>.</div>";
                echo "</div>";
                echo "<a href='" . Wi3::$urlof->page(Wi3::$page->title) . "/" . $item->slug . "'>uitgebreid...</a>";
            echo "</div>";
        }
    }

?>