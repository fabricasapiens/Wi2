<ul>
    <?php
    
    $menu = Array(
        "engine/controlpanel" => "Overzicht",
        "engine/menu" => "Menu",
        "engine/content" => "Inhoud",
        "engine/files" => "Bestanden",
        //"engine/view_site" => "Bekijk site"
    );
    
    //enable user-management if the user is allowed to do so
    if (Wi3::$rights->check("admin", Wi3::$site)) {
        $menu["engine/users"] = "Gebruikers";
    }
    
    //disable filemanagement if it is disabled for this user
    /*
    if ($site->filemanagement == "no") {
        unset($menu["engine/files"]);
    }*/
    
    foreach($menu as $urllink => $urltext) {
        $navpart = substr($urllink, strrpos($urllink, "/")+1);
        echo "<li" .  ( (isset($navigation_active) AND $navigation_active == $navpart) ? " class='active'" : "") . ">" .  html::anchor($urllink, $urltext) . "</li>";
    }
    
    //echo "<li>" . html::anchor("engine/view_site","3. view site",array("target" => "_blank") ) . "</li>";

    ?>

</ul>