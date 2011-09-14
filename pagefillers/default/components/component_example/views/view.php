<?php

    echo "<table>";
    echo "<tr><th style='padding-right: 10px;'>id</th><th>name</th><th>text</th></tr>";
    foreach($items as $id => $item) {
        echo "<tr><td>" . $id .  "</td><td><img src='" . Wi3::$urlof->image($item->foto, 30) .  "'></img></td><td>" . $item->name . "</td><td>" . $item->text. "</td></tr>";
    }
    echo "</table>";

?>