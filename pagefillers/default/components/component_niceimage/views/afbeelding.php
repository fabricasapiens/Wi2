<h1>Selecteer een afbeelding</h1>
<p>
<?php
    foreach($images as $image) {
        echo "<div class='component_niceimage_selectableimage' style='float: left;'>";
            echo "<a href='javascript:void(0)' style='text-decoration: none;' onClick='return wi3_component_afbeelding_choose(" . $image->id . ");'>";
            echo "<div class='component_niceimage_selectableimage_image'><img src='" . Wi3::$urlof->image($image->filename, 100) . "'></img></div><span>" . $image->filename . "</span></a>";
        echo "</div>";
    }
?>
<div style='visibility: hidden; clear:both; font-size: 1px;'>.</div>
</p>