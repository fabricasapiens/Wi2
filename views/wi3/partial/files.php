<?php
    
    $site = Wi3::$site;

    //if (true) {
        echo "<div id='wi3_prullenbak'>";
            //echo "<div id='prullenbak_boven'></div>";
            echo "<div id='prullenbak_onder'><h1>Verwijderen</h1>Sleep een bestand naar de prullenbak om deze te verwijderen.</div>";
        echo "</div>";
        echo "<div id='wi3_add_file'><h1>Toevoegen</h1>";?>
            <table>
                <tr>
                    <form enctype="multipart/form-data" method="post">
                        <td>bestand</td>
                        <td><input type="file" value="" name="file" id="file"/></td>
                        <td><input type="submit" value="+" name="submit" id="submit"/></td>
                    </form>
                </tr>
                
                <tr>
                    <form method="post">
                        <td>map</td>
                        <td><input type="folder" value="" name="folder" id="folder"/></td>
                        <td><input type="submit" value="+" name="submit" id="submit"/></td>
                    </form>
                </tr>
            </table>

        <? echo "</div>";
    //}
    echo "<ul id='files_files' style='position: relative;' class='simpleTree'><li class='root'><span></span><ul>";
    if (count($files) > 0) {
        foreach($files as $file) {
            echo "<li ". ( $file->type == "folder" ? "folder='folder'" : "") . " id='treeItem_" . $file->id . "'><span>" . html::anchor("engine/content/" . $file->id, $file->title) . "</span>";
            echo render_children_as_list($file);
            echo "</li>";
        }
    }
    echo "</ul></li></ul>";
    
    //render the container in which the file properties will appear when a file is single-clicked
    echo "<div id='files_filesettings'>";
         echo "<div id='files_filesettings_tabs'>";
    
        echo "</div>";
    echo "</div>";
    
    function render_children_as_list($page) {
        //recursive
        $ret = "";
        if (count($page->children) > 0) {
            $ret .= "<ul>";
            $children = $page->children;
            foreach($children as $child) {
                //render as link, although Javascript will remove the href and will ceate a DblClick and OnClick for the pages
                $ret .= "<li class='treeItem' id='treeItem_" . $child->id . "'><span>" . html::anchor("engine/content/" . $child->id, $child->title) . "</span>";
                $ret .= render_children_as_list($child);
                $ret .= "</li>";
            }
            $ret .= "</ul>";
        } else {
            //insert fake file if the containing element is supposed to be a folder
            //the fake element will maken dropping on it work
            if ($page->type == "folder") {
                $ret .= "<ul><li action='remove'><span></span></li></ul>";
            }
        }
        return $ret;
}

?>
