<?php

    if (isset($addpages)) {
        //echo "<div id='wi3_prullenbak'>";
        //    echo "<div id='prullenbak_onder'><h1>Verwijderen</h1>Sleep een gebruiker naar de prullenbak om deze te verwijderen.</div>";
        //echo "</div>";
        echo "<div id='wi3_add_user'><h1>Toevoegen</h1><form id='wi3_add_user_form' method='POST' onSubmit='return false;'>
            <label for='newuser_username'>gebruikersnaam</label><input id='newuser_username' name='newuser_username' /><br />
            <label for='newuser_password'>wachtwoord</label><input id='newuser_password' name='newuser_password' /> 
            <button onClick='wi3.request(\"ajaxengine/addUserToSite\", $(\"#wi3_add_user_form\").serializeArray())'>toevoegen</button>
        </form></div>";
            
    }
    echo "<ul id='users_users' style='position: relative;' class='simpleTree'><li class='root'><span></span><ul>";
    foreach($users as $user) {
        echo "<li class='treeItem' style='cursor: pointer;' id='treeItem_" . $user->id . "'><span>" . $user->username . "</span>";
        echo "</li>";
    }
    echo "</ul></li></ul>";
    
    //render the container in which the user properties will appear when a page is single-clicked
    echo "<div id='users_usersettings'>";
         echo "<div id='users_usersettings_tabs'>";
    
        echo "</div>";
    echo "</div>";

?>
