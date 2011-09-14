<?php

    if (isset($item) AND !isset($items)) {
        //display one certain article, not all 
        echo "<div class='component_calendar_gotoallitems'><a href='" . Wi3::$urlof->page(Wi3::$page->url) . "'>terug naar agenda overzicht...</a></div>";
        echo "<div class='component_calendar_item'>";
            //date is most important
            echo "<div class='component_calendar_date'><div class='component_calendar_date_day'>" . substr($item->sortabledate, 6,2) . "</div><div class='component_calendar_date_month'>" . substr($item->sortabledate, 4,2) . " " . substr($item->sortabledate, 0,4) . "</div>";
            echo "</div>";
            //echo "<a href='" . Wi3::$urlof->page(Wi3::$page->title) . "/" . $item->slug . "'>lees meer...</a>";
            echo "<div class='component_calendar_content'>";
                echo "<h2>" . $item->title . "</h2>";
                echo "<div>";
                    echo $item->fullText;
                    echo "<div class='component_calendar_floatfix'>.</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    } else if (isset($items)) {
        //todo: pagination
        foreach($items as $id => $item) {
            echo "<div class='component_calendar_item'>";
                //date is most important
                echo "<div class='component_calendar_date'><div class='component_calendar_date_day'>" . substr($item->sortabledate, 6,2) . "</div><div class='component_calendar_date_month'>" . substr($item->sortabledate, 4,2) . " " . substr($item->sortabledate, 0,4) . "</div></div>";
                //echo "<a href='" . Wi3::$urlof->page(Wi3::$page->title) . "/" . $item->slug . "'>lees meer...</a>";
                echo "<div class='component_calendar_content'>";
                    echo "<a href='" . Wi3::$urlof->page(Wi3::$page->url) . "/" . $item->slug . "'><h2>" . $item->title . "</h2></a>";
                    echo "<div>";
                        echo $item->summary;
                        echo "<div class='component_calendar_floatfix'>.</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        }
    }

?>
