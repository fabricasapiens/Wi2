<?php
    //buffer content of the 'general' tab
    ob_start();
?>

<?php 

    $page = ORM::factory("page", $pageid);
    Wi3::$page = $page;
    Event::run("wi3.siteandpageloaded");
    //aangeven welke pagine op dit moment gewijzigd wordt 
    echo "<div id='wi3_edited_page' style='display: none;'>" . $pageid . "</div>";
    echo "<form id='wi3_pageedit_form' onsubmit='wi3.request(\"ajaxengine/editPageSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_form\").serializeArray()); return false;'>";
    
    echo "<h2>Pagina bekijken en wijzigen</h2>";
    echo "<p><label for='link'>links: </label><span name='viewlink'><a target='_blank' href='" . Wi3::$urlof->page . "'>bekijken</a></span> <span name='editlink'><a href='" . Wi3::$urlof->wi3 . "engine/content/" . $page->id. "'>wijzigen</a></span></p>";
    
    echo "<h2>Pagina-instellingen</h2>";
    if (Wi3::$rights->check("edit", $page) == true) {
        echo "<p><label for='pagetitle'>paginatitel: </label><input name='pagetitle' id='pagetitle' type='text' value='" . $page->title . "' /></p>";
        echo "<p><label for='visible'>zichtbaar in menu: </label><select name='visible' id='visible' value='" . $page->visible . "'>" . Wi3::optionlist(array("1"=>"zichtbaar", "0"=>"verborgen"), $page->visible) . "</select></p>";
    } else {
        echo "<p>U hebt niet de benodige rechten om de pagina-instellingen aan te passen.</p>";
    }
    
    //check whether user is allowed to set the rights of a page
    //one would need to be admin for that. (that means: admin, siteadmin, having the 'adminright' or being the owner of the page)
     echo "<h2>Rechten-instellingen</h2>";
    if (Wi3::$rights->check("admin", $page) == true) {
        //so this user has admin rights, show the different rights
        echo "<p>
        <label for='viewright'>voor bekijken: </label><input name='viewright' id='viewright' type='text' value='" . $page->viewright . "' /><br />
        <label for='editright'>voor wijzigen: </label><input name='editright' id='editright' type='text' value='" . $page->editright . "' /><br />
        <label for='adminright'>voor admin: </label><input name='adminright' id='adminright' type='text' value='" . $page->adminright . "' /><br />
        </p>";
    } else {
        echo "<p>U hebt niet de benodige rechten om de rechten-instellingen aan te passen.</p>";
    }
    
    //get all root-pages (which are the languages)
	/*
	(leuk, maar we hebben de héle tree nodig)
	$children=$page->new_node();
	$children->where($page->parent_column,null); //pagina's zonder parent zijn de roots
	$children->where($page->scope_column,$page->get_scope());   
	$children->orderby($page->left_column);
    $availablelanguages = $children->find_all(); //pakt alle objecten met parent_id = $falseroot->id;
    */
    
    $falseroot = ORM::factory("page");
    $falseroot->leftnr = 0; $falseroot->rightnr = "99999999999999999999"; $falseroot->set_scope($page->get_scope());
    $pages = $falseroot->get_tree($falseroot);
    
    //make the visibility of the page editable
    $modules = $site->modules;
    if (is_array($modules) AND isset($modules["page_choose_visibility"])) {
        echo "<p><input name='page_visibility' id='page_visibility' type='checkbox' " . ($page->visible == true ? "checked='checked'" : "") . " />pagina zichtbaar in menu</input></p>";
    }
    
    //check whether we can edit the language settings
    /*
    if (is_array($modules) AND isset($modules["site_multilanguage"])) {
    
        //eigen taal bepalen
        $currlang = $page->get_path();
        $currlang = $currlang[0];
        
        //ingestelde taalpagina van deze pagina per taal ophalen
        $langarray = unserialize($page->get("wi3_language_array"));
        
        if (count($falseroot->children) > 1) { //als er meer dan 1 taal is
        
            echo "<h2>Taalinstellingen</h2>";
            echo "<p>Deze pagina correspondeert met:</p><table>";
            
            foreach($falseroot->children as $lang) {
                if ($lang->id != $currlang->id) {
                    echo "<tr><td>". $lang->title . "</td><td><select name='lang_" . $lang->id . "' ";
                    if (isset($langarray[$lang->id])) { 
                        echo "value='" . $langarray[$lang->id] . "'";
                        $activeid = $langarray[$lang->id];
                    } else {
                        $activeid = -1;
                    }
                    echo "><option value=''>-geen-</option>";
                    echo printoptions($lang->children, $activeid);
                    echo "</select></td><td></td></tr>";
                }
            }
            echo "</table>";
            
        }
        
    } //end if editing language settings is allowed
    */
    
    function printoptions($tree, $activeid) {
        $ret = "";
        if (!empty($tree)) {
            foreach($tree as $child) {
                $ret .= "<option value='" . $child->id . "' "; 
                if ($child->id == $activeid) { $ret .= "selected='selected'"; }
                $ret .= ">" . $child->title . "</option>";
                $ret .= printoptions($child->children, $activeid);
            }
        }
        return $ret;
    }
    
    echo "</form>";
    
    //opslaan knop
    echo " <button onClick='wi3.request(\"ajaxengine/editPageSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_form\").serializeArray());'>opslaan</button>";
    
    //end of general tab buffer
    $content_general = ob_get_contents();
    ob_end_clean();

    //--- 
    // The template part
    // ---
    ob_start();
    //one would need to have edit-rights to edit the template-settings of the page
    echo "<form id='wi3_pageedit_template_form' onsubmit='wi3.request(\"ajaxengine/editPageTemplateSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_template_form\").serializeArray()); return false;'>";
    echo "<h2>Template instellingen</h2>";
    if (Wi3::$rights->check("edit", $page) == true) {
        //so this user has edit rights, show the different template settings
        echo "<p>
        <label for='id_templatetype'>template categorie </label><select name='templatetype' id='id_templatetype' value='" . $page->page_templatetype . "'>";
            echo "<option value='user'>site-specifiek</option><option value='wi3'>standaard wi3 templates</option>";
        echo "</select><br />";
        echo "<label for='id_template'>template naam </label>";
        //input variant
        echo "<input type='text' name='template' id='id_template' value='" . $page->page_template. "' />";
        //select variant
        /*
        echo "<select name='template' id='id_template' value='" . $page->page_template. "' >";
            $usertemplatedir = Wi3::$pathof->sitetemplates;
            $usertemplates = "";
            $files = glob($usertemplatedir . "*.php");
            foreach($files as $filename) {
                $templatename = substr($filename, strrpos($filename, "/")+1, -4);
                if ($site->default_page_templatetype == "user" AND $site->default_page_template == $templatename) {
                    $usertemplates .= "<a class='bold' id='template_user_" . $templatename .  "' href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/useUserTemplate/" . $templatename . "\", {});'>" . $templatename . "</a> ";
                } else {
                    $usertemplates .= "<a id='template_user_" . $templatename .  "' href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/useUserTemplate/" . $templatename . "\", {});'>" . $templatename . "</a> ";
                }
            }
        echo "</select><br />";
        */
        echo "</p>";
    } else {
        echo "<p>U hebt niet de benodige rechten om de rechten-instellingen aan te passen.</p>";
    }
    echo "</form>";
    //save button
    echo " <button onClick='wi3.request(\"ajaxengine/editPageTemplateSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_template_form\").serializeArray());'>opslaan</button>";
    $content_template = ob_get_contents();
    ob_end_clean();
    
    // Redirect part
    // Doorverwijzen naar externe link of naar een andere pagina
    ob_start();
    //one would need to have edit-rights to edit the template-settings of the page
    echo "<form id='wi3_pageedit_redirect_form' onsubmit='wi3.request(\"ajaxengine/editPageRedirectSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_redirect_form\").serializeArray()); return false;'>";
    echo "<h2>Doorverwijzingen</h2>";
    if (Wi3::$rights->check("edit", $page) == true) {
        echo "<p>";
        echo "<label for=''>pagina doorverwijzen? </label><select name='redirect_type' id='redirect_type' value='" . $page->page_redirect_type . "'>" . Wi3::optionlist(array("none"=>"nee. Eigen inhoud pagina.", "wi3" => "ja. Naar wi3 pagina.", "external"=>"ja. Naar externe URL."), $page->page_redirect_type) . "</select><br />";
        global $pagelist;
        $pagelist = array();
        function pagelist($cpage) {
            global $pagelist;
            foreach($cpage->children as $p) {
                $pagelist[$p->id] = $p->title;
                if (is_array($p->children) AND !empty($p->children)) {
                    pagelist($p);
                }
            }
        }
        pagelist($pages);
        echo "<label for=''>verwijzen naar wi3 pagina </label><select name='redirect_wi3' id='redirect_wi3' value='" . $page->page_redirect_wi3 . "'>" . Wi3::optionlist( $pagelist, $page->page_redirect_wi3) . "</select><br /><br />";
        echo "<label for='redirect_external'>verwijzen naar URL</label><input type='text' name='redirect_external' id='redirect_external' value='" . $page->page_redirect_external. "' />";
        echo "</p>";
        
    } else {
        echo "<p>U hebt niet de benodige rechten om een doorverwijzing in te stellen.</p>";
    }
    echo "</form>";
    //save button
    echo " <button onClick='wi3.request(\"ajaxengine/editPageRedirectSettings/\" + $(\"#wi3_edited_page\").html(), $(\"#wi3_pageedit_redirect_form\").serializeArray()); return false;'>opslaan</button>";
    $content_redirect = ob_get_contents();
    ob_end_clean();
    
    //now render the tabs
?>

    <ul>
		<li><a href="#general">Algemeen</a></li>
		<li><a href="#template">Template</a></li>
        <li><a href="#redirect">Doorverwijzing</a></li>
		<li style='display: none;'><a href="#export">Exporteren</a></li>
	</ul>
	<div id="general">
		<?php echo $content_general; ?>
	</div>
	<div id="template">
        <?php echo $content_template; ?>
	</div>
    <div id="redirect">
        <?php echo $content_redirect; ?>
	</div>
	<div id="export">
		<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
		<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
	</div>