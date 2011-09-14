<?php
    //buffer content of the 'general' tab
    ob_start();
?>

<?php 

    //preview of the file and a link to the file
    echo "<h2>Voorvertoning en link</h2><p>";
    $url = Wi3::$urlof->site . "data/files/" . $file->filename;
    preg_match("@.*\.([^\.]+)$@", $file->filename, $matches);
    $extension = $matches[1];
    //create preview
        //if image
        if (in_array($extension, array("jpg", "png", "bmp", "gif", "jpeg"))) {
            //show preview image
            //echo "<img src='" . Wi3::$urlof->image($file->filename) . "'/>";
        } else {}
        //if sound
        
        //if PDF
        
        //if other
    echo html::anchor($url, $file->title, array("target" => "_blank"));
    echo "</p>";
    
    if ($extension == "zip") {
        //give the possibility to unpack this file
        echo "<h2>Dit is een zip bestand. U kunt deze uitpakken.</h2>";
        echo "<p><a href='" . Wi3::$urlof->wi3 . "engine/unpackfile/" . $file->id . "'>uitpakken in huidige map</a></p>";
    }

    //aangeven welke paginaop dit moment gewijzigd wordt
    echo "<div id='wi3_edited_file' style='display: none;'>" . $file->id . "</div>";
    echo "<form id='wi3_fileedit_form' onsubmit='wi3.request(\"ajaxengine/editFileSettings/\" + $(\"#wi3_edited_file\").html(), $(\"#wi3_fileedit_form\").serializeArray()); return false;'>";
    echo "<h2>Bestands-instellingen</h2>";
    if (Wi3::$rights->check("edit", $file) == true) {
        echo "<p><label for='filename'>bestandsnaam: </label><span name='filename' id='filename' >" . $file->filename . "</span><br />
        <label for='title'>bestandstitel: </label><input name='title' id='title' type='text' value='" . $file->title . "' /></p>";
    } else {
        echo "<p>U hebt niet de benodige rechten om de bestands-instellingen aan te passen.</p>";
    }
    
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
    }
    
    echo "</form>";
    
    //opslaan knop
    echo " <button onClick='wi3.request(\"ajaxengine/editFileSettings/\" + $(\"#wi3_edited_file\").html(), $(\"#wi3_fileedit_form\").serializeArray());'>opslaan</button>";
    
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
	<div id="theme">
	</div>
	<div id="export">
		<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
		<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
	</div>