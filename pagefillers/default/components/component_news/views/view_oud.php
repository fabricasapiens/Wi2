<?php

    if (isset($item) AND !isset($items)) {
        //display one certain article, not all 
        echo "<div>";
            echo "<h2>" . $item->title . "</h2>";
            echo "<div>";
                echo "<img src='" . Wi3::$urlof->image($item->mainImage, 250) . "'></img>";
                echo $item->fullText;
                echo "<div class='component_news_floatfix'>.</div>";
            echo "</div>";
            echo "<a href='" . Wi3::$urlof->page(Wi3::$page->title) . "'>terug...</a>";
        echo "</div>";
    } else if (isset($items)) {
        //todo: pagination
        foreach($items as $id => $item) {
            echo "<div>";
                echo "<h2>" . $item->title . "</h2>";
                echo "<div>";
                    echo "<img src='" . Wi3::$urlof->image($item->mainImage, 150) . "'></img>";
                    echo $item->summary;
                    echo "<div class='component_news_floatfix'>.</div>";
                echo "</div>";
                echo "<a href='" . Wi3::$urlof->page(Wi3::$page->title) . "/" . $item->slug . "'>lees meer...</a>";
            echo "</div>";
        }
    }

?>