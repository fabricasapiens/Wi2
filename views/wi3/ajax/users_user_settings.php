<?php
    //buffer content of the 'general' tab
    ob_start();
?>

<?php 

    echo "<div id='wi3_edited_user' style='display: none;'>" . $user->id . "</div>";
    echo "<form id='wi3_useredit_form' onsubmit='wi3.request(\"ajaxengine/editUserSettings/\" + $(\"#wi3_edited_user\").html(), $(\"#wi3_useredit_form\").serializeArray()); return false;'>";

    echo "<h2>Gebruikersnaam</h2>";
    if (Wi3::$rights->check("admin", Wi3::$site) == true) {
        echo "<p><label for='username'>gebruikersnaam: </label><input name='username' id='username' value='" . $user->username . "'/></span></p>";
    } else {
        echo "<p>" . $user->username . "</p>";
    }
    
    //opslaan knop
    echo "</form>";
    echo "<p><button onClick='wi3.request(\"ajaxengine/editUserSettings/\" + $(\"#wi3_edited_user\").html(), $(\"#wi3_useredit_form\").serializeArray());'>wijzigen</button></p>";
    
    echo "<h2>Rollen</h2>";
    if (Wi3::$rights->check("admin", Wi3::$site) == true) {
        echo "<table id='userroles_list'>";
        $counter = 0;
        foreach($user->roles as $role) {
            $counter++;
            echo "<tr name='role_" . $role->id . "' id='role_" . $role->id . "'><td>" . $counter . ".</td><td><span>" . $role->name . "</span></td><td><a href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/revokeUserRole/\", {userid: " . $user->id . ", roleid: " . $role->id . "}); return false;'>ontkoppelen van gebruiker</a></td></tr>";
        }
        echo "</table>";
        echo "<form id='wi3_userroleedit_form' onsubmit='wi3.request(\"ajaxengine/addUserRole/\", {userid: " . $user->id . ", rolename: $(\"#newrole\").val()}); return false;'>";
        echo "<p><label for='newrole'>nieuwe rol: </label><input name='newrole' id='newrole' value=''/></span></p>";
        echo "</form>";
        echo "<p><button onClick='wi3.request(\"ajaxengine/addUserRole/\", {userid: " . $user->id . ", rolename: $(\"#newrole\").val()});'>toevoegen</button></p>";
    } else {
        echo "<p>U hebt niet het recht de toegekende rollen in te zien of te wijzigen.</p>";
    }
    
    echo "<h2>Wachtwoord wijzigen</h2>";
     if (Wi3::$rights->check("admin", Wi3::$site) == true) {
        echo "<p><label for='changepassword'>nieuw wachtwoord </label><input name='changepassword' id='changepassword' value=''/></span></p>";
        echo "<p><button onclick='wi3.request(\"ajaxengine/changePassword/\", {userid: " . $user->id . ", password: $(\"#changepassword\").val()});'>nieuw wachtwoord instellen</button></p>";
     }
    
     echo "<h2>Verwijderen</h2>";
     if (Wi3::$rights->check("admin", Wi3::$site) == true) {
        echo "<p style='color: #ff0000;'>LET OP: dit verwijdert de gebruiker definitief! Er is geen weg terug:</p>";
        echo "<p><button onclick='wi3.request(\"ajaxengine/removeUser/\", {userid: " . $user->id . "});'>gebruiker verwijderen</button></p>";
     }
    
    /*
    //check whether user is allowed to set the rights of a page
    //one would need to be admin for that. (that means: admin, siteadmin, having the 'adminright' or being the owner of the page)
     echo "<h2>Rechten-instellingen</h2>";
    if (Wi3::$rights->check("admin", $file) == true) {
        //so this user has admin rights, show the different rights
        echo "<p>
        <label for='viewright'>voor bekijken: </label><input name='viewright' id='viewright' type='text' value='" . $file->viewright . "' /><br />
        <label for='editright'>voor wijzigen: </label><input name='editright' id='editright' type='text' value='" . $file->editright . "' /><br />
        <label for='adminright'>voor admin: </label><input name='adminright' id='adminright' type='text' value='" . $file->adminright . "' /><br />
        </p>";
    } else {
        echo "<p>U hebt niet de benodige rechten om de rechten-instellingen aan te passen.</p>";
    }*/
    
   
    
    //end of general tab buffer
    $content_general = ob_get_contents();
    ob_end_clean();
    
    //now render the tabs
?>

    <ul>
		<li><a href="#general">Algemeen</a></li>
		<li style='display: none;'><a href="#theme">Uiterlijk</a></li>
		<li style='display: none;'><a href="#export">Exporteren</a></li>
	</ul>
	<div id="general">
		<?php echo $content_general; ?>
	</div>
