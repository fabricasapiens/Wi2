<?php

    if (isset($pagetypes)) {
        echo "<div id='wi3_prullenbak'>";
            //echo "<div id='prullenbak_boven'></div>";
            echo "<div id='prullenbak_onder'><h1>Verwijderen</h1>Sleep een pagina naar de prullenbak om deze te verwijderen.</div>";
        echo "</div>";
        echo "<div id='wi3_add_pages'><h1>Toevoegen</h1><div id='wi3_add_pages_options'>";
        $nr = 0;
        $amountpercolumn = 3;
        foreach($pagetypes as $index => $pagetype) {
            if ($nr % $amountpercolumn == 0) {
                echo "<div class='wi3_add_pages_pagetype'>";
            }
            echo "<a href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/addPage/" . $index . "/\" + workplace.currentTree().getSelected().attr(\"id\") , {})'>" . ucfirst($pagetype["title"])  .  "</a> <br />";
            if ($nr % $amountpercolumn == ($amountpercolumn-1) OR $nr == count($pagetypes)-1) {
                echo "</div>"; //end of pagetype column
            }
            $nr++;
        }
        echo "</div></div>";
    }
    echo "<ul id='menu_pages' style='position: relative;' class='simpleTree'><li class='root'><span></span><ul>";
    if ($imaginaryroot->children) {
        $pages = $imaginaryroot->children;
        foreach($pages as $page) {
            echo "<li class='treeItem' id='treeItem_" . $page->id . "'><span>" . html::anchor("engine/content/" . $page->id, $page->title) . "</span>";
            echo render_children_as_list($page);
            echo "</li>";
        }
    }
    echo "</ul></li></ul>";
    //render the container in which the page properties will appear when a page is single-clicked
    echo "<div id='menu_pagesettings'>";
         echo "<div id='menu_pagesettings_tabs'>";
    
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
                $ret .= "<li class='treeItem' id='treeItem_" . $child->id . "'><span>" . html::anchor("engine/content/" . $child->id, $child->title) . ($child->visible === "nadfasdflkjsdf" ? "<span style='font-size: 10px; color: #ff0000;'> (verborgen) </span>" : "") . "</span>";
                $ret .= render_children_as_list($child);
                $ret .= "</li>";
            }
            $ret .= "</ul>";
        }
        return $ret;
}

?>
