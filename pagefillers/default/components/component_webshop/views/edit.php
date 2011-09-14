<?php 
    //set component type and ID as prefix for element ID's
    $prefix = $field->type; 
?>
<div id="<?php echo $prefix; ?>_edit_tabs">
	<ul>
        <li><a href="#<?php echo $prefix; ?>_edit_tabs-1">Instellingen</a></li>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-2">Nieuw artikel</a></li>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-3">Wijzigen/verwijderen</a></li>
	</ul>
	<div id="<?php echo $prefix; ?>_edit_tabs-1">
        <form id='<?php echo $prefix; ?>_settings' onSubmit='return wi3.pagefiller.components.<?php echo $prefix; ?>.savesettings();'><table>
            <tr><td colspan='2'>emailadres waar bestellingen heengestuurd worden</td></tr>
            <?php
            
                //fetch the formsettings-array
                $settings = $field->get_sqlarray( array("where" => array("arrayname" => "settings") ) );
                if (!is_object($settings)) {
                    $settings = $field->new_sqlarray( array("arrayname" => "settings") );
                }
            
            ?>
            <tr><td>emailadres</td><td><input name='email' value='<?php echo $settings->email; ?>' /></td></tr>
            <tr><td> </td><td><button onClick='return wi3.pagefiller.components.<?php echo $prefix; ?>.savesettings();'>instellingen opslaan</button></td></tr>
        </table>
        </form>
    </div>
    <div id="<?php echo $prefix; ?>_edit_tabs-2">
        <?php //the button to add an element ?>
        <h2>Nieuw artikel</h2><p>
            <div id="<?php echo $prefix; ?>_article_tabs">
                <ul>
                    <li><a href="#<?php echo $prefix; ?>_article_tabs-1">CD</a></li>
                    <li><a href="#<?php echo $prefix; ?>_article_tabs-2">Anders</a></li>
                </ul>
                <div id="<?php echo $prefix; ?>_article_tabs-1">
                    <form id='<?php echo $prefix; ?>_add' onSubmit='return wi3.pagefiller.components.<?php echo $prefix; ?>.add();'><table>
                        <?php 
                            // -----------------------------------
                            // edit the part below to create the add-screen
                            // copy it to the edit-part to make editing work just like that
                            // -----------------------------------
                        ?>
                        <? //<tr><td> datum </td><td> <input name='date' class='componentInput_datepicker' /></td></tr> ?>
                        <input type='hidden' name='type' value='cd' />
                        <tr><td> naam </td><td> <input name='name' /></td></tr>
                        <tr><td> foto </td><td> <select name='productImage'>
                        <?php
                            //push a list with images into the select
                            $images = Wi3::$files->find(array("whereExt" => array("jpg", "jpeg", "png", "bmp", "gif")));
                            foreach($images as $image) {
                                //<img src='" . Wi3::$urlof->image($image->filename,10) . "'/> in the option crashes Google Chrome :s
                                echo "<option value='" . $image->filename . "'>" . $image->title . "</option>";
                            }
                        ?>
                        </select></td></tr>
                        <tr><td> prijs </td><td> <input name='price' /></td></tr>
                        <tr><td> beschrijving </td><td> <textarea name='description' class='componentInput_tinyMCE' /></td></tr>
                        <strong>Nummers</strong>
                        <?php
                        //push a list with images into the select
                        $files = Wi3::$files->find(array("whereExt" => array("mp3", "mp4", "flv", "wav", "ogg", "aac", "wma")));
                        $fileselect = "<option value='nofile'>-</option>";
                        foreach($files as $file) {
                            //<img src='" . Wi3::$urlof->image($image->filename,10) . "'/> in the option crashes Google Chrome :s
                            $fileselect .= "<option value='" . $file->filename . "'>" . $file->title . "</option>";
                        }
                        //show 20 tracks, along with the ability to enter titles and linked file
                        for($i=1;$i<21;$i++) {
                            echo "<tr><td>" . $i . ".</td><td><input name='track_" . $i . "_title' /></td><td><select class='component_webshop_linkedfile' name='track_" . $i . "_linkedfile'><option value='nofile'>geen audiofragment</option>" . $fileselect . "</select></td></tr>";
                            //echo "<tr><td>fragment</td><td><select name='track_" . $i . "_linkedfile'><option value='nofile'>geen audiofragment</option>" . $fileselect . "</select></td></tr>";
                        }
                            // -----------------------------------
                            // end of editable part
                            // -----------------------------------
                        ?>
                        <tr><td> </td><td><button onClick='return wi3.pagefiller.components.<?php echo $prefix; ?>.add();'>toevoegen</button></td></tr>
                    </table></form>
                </div>
                <div id="<?php echo $prefix; ?>_article_tabs-2">
                    Ander artikel
                </div>
            </div>
        </p>
        
    </div>
    <div id="<?php echo $prefix; ?>_edit_tabs-3">
        <?php
        
           echo "<h2>Bestaande artikelen</h2>";
           ?>
           
           <form id='<?php echo $prefix; ?>_edit' onSubmit='return wi3.pagefiller.components.<?php echo $prefix; ?>.saveedit();'>
           <input type='hidden' name='__id' />
           <table>
                <?php 
                    // -----------------------------------
                    // edit the part below to create the add-screen
                    // copy it to the edit-part to make editing work just like that
                    // -----------------------------------
                ?>
                <? //<tr><td> datum </td><td> <input name='date' class='componentInput_datepicker' /></td></tr> ?>
                        <input type='hidden' name='type' value='cd' />
                        <tr><td> naam </td><td> <input name='name' /></td></tr>
                        <tr><td> foto </td><td> <select name='productImage'>
                        <?php
                            //push a list with images into the select
                            $images = Wi3::$files->find(array("whereExt" => array("jpg", "jpeg", "png", "bmp", "gif")));
                            foreach($images as $image) {
                                //<img src='" . Wi3::$urlof->image($image->filename,10) . "'/> in the option crashes Google Chrome :s
                                echo "<option value='" . $image->filename . "'>" . $image->title . "</option>";
                            }
                        ?>
                        </select></td></tr>
                        <tr><td> prijs </td><td> <input name='price' /></td></tr>
                        <tr><td> beschrijving </td><td> <textarea name='description' class='componentInput_tinyMCE' /></td></tr>
                        <strong>Nummers</strong>
                        <?php
                        //push a list with images into the select
                        $files = Wi3::$files->find(array("whereExt" => array("mp3", "mp4", "flv", "wav", "ogg", "aac", "wma")));
                        $fileselect = "<option value='nofile'>-</option>";
                        foreach($files as $file) {
                            //<img src='" . Wi3::$urlof->image($image->filename,10) . "'/> in the option crashes Google Chrome :s
                            $fileselect .= "<option value='" . $file->filename . "'>" . $file->title . "</option>";
                        }
                        //show 20 tracks, along with the ability to enter titles and linked file
                        for($i=1;$i<21;$i++) {
                            echo "<tr><td>" . $i . ".</td><td><input name='track_" . $i . "_title' /></td></tr>";
                            echo "<tr><td>fragment</td><td><select name='track_" . $i . "_linkedfile'>" . $fileselect . "</select></td></tr>";
                        }
                    // -----------------------------------
                    // end of editable part
                    // -----------------------------------
                ?>
                <tr><td> </td><td><button onClick='return wi3.pagefiller.components.<?php echo $prefix; ?>.saveedit();'>wijzigen</button></td></tr>
            </table></form>
            
            <?php
           echo "<table id='" . $prefix . "_existing'><tr><th>naam</th><th>actie</th><th>actie</th><th>actie</th></tr>";
            foreach($items as $id => $item) {
                echo "<tr id='" . $prefix . "_item_" . $id . "'><td>" . $item->name . "</td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".edit(\"" . $id . "\");'>wijzigen</a></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".moveup(this);'>naar boven</a></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".movedown(this);'>naar onderen</a></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".remove(\"" . $id . "\");'><strong>verwijderen</strong></a></td>";
                //expose information of the elements, so that they can be edited
                echo "<td style='display: none;'>";
                    //iterate over the array and render its info
                    $localprefix = $prefix . "_field_" . $id . "_";
                    foreach($item as $key => $val) {
                        echo "<span id='" . $localprefix . $key . "' name='" . $key . "'>" . $val . "</span>";
                    }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        ?>
    </div>
</div>