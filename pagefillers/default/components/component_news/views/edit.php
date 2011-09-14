<?php 
    //set component type and ID as prefix for element ID's
    $prefix = $field->type; 
?>
<div id="<?php echo $prefix; ?>_edit_tabs">
	<ul>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-1">Nieuw</a></li>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-2">Wijzigen/verwijderen</a></li>
	</ul>
	<div id="<?php echo $prefix; ?>_edit_tabs-1">

        <?php //the button to add an element ?>
        <h2>Nieuw element</h2><p>
                <form id='<?php echo $prefix; ?>_add' onSubmit='return wi3.pagefiller.components.<?php echo $prefix; ?>.add();'><table>
                <?php 
                    // -----------------------------------
                    // edit the part below to create the add-screen
                    // copy it to the edit-part to make editing work just like that
                    // -----------------------------------
                ?>
                <tr><td> titel </td><td> <input name='title' /></td></tr>
                <tr><td> foto </td><td> <select name='mainImage'>
                <?php
                    //push a list with images into the select
                    $images = Wi3::$files->find(array("whereExt" => array("jpg", "jpeg", "png", "bmp", "gif")));
                    foreach($images as $image) {
                        echo "<option value='" . $image->filename . "'>" . $image->title . "</option>";
                    }
                ?>
                </select></td></tr>
                <tr><td> samenvatting </td><td> <textarea name='summary' /></td></tr>
                <tr><td> volledige tekst </td><td> <textarea name='fullText' /></td></tr>
                <?php
                    // -----------------------------------
                    // end of editable part
                    // -----------------------------------
                ?>
                <tr><td> </td><td><button onClick='return wi3.pagefiller.components.<?php echo $prefix; ?>.add();'>toevoegen</button></td></tr>
            </table></form>
        </p>
        
    </div>
    <div id="<?php echo $prefix; ?>_edit_tabs-2">
        <?php
        
           echo "<h2>Bestaande elementen</h2>";
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
                <tr><td> titel </td><td> <input name='title' /></td></tr>
                <tr><td> foto </td><td> <select name='mainImage'>
                <?php
                    //push a list with images into the select
                    $images = Wi3::$files->find(array("whereExt" => array("jpg", "jpeg", "png", "bmp", "gif")));
                    foreach($images as $image) {
                        echo "<option value='" . $image->filename . "'>" . $image->title . "</option>";
                    }
                ?>
                </select></td></tr>
                <tr><td> samenvatting </td><td> <textarea name='summary' /></td></tr>
                <tr><td> volledige tekst </td><td> <textarea name='fullText' /></td></tr>
                <?php
                    // -----------------------------------
                    // end of editable part
                    // -----------------------------------
                ?>
                <tr><td> </td><td><button onClick='return wi3.pagefiller.components.<?php echo $prefix; ?>.saveedit();'>wijzigen</button></td></tr>
            </table></form>
            
            <?php
           echo "<table id='" . $prefix . "_existing'><tr><th>naam</th><th>actie</th><th>actie</th><th>actie</th></tr>";
            foreach($items as $id => $item) {
                echo "<tr id='" . $prefix . "_item_" . $id . "'><td>" . $item->title . "</td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".edit(\"" . $id . "\");'>wijzigen</a></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".moveup(this);'>naar boven</a></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".movedown(this);'>naar onderen</a></td><td><a href='javascript:void(0)' onClick='wi3.pagefiller.components." . $prefix . ".remove(\"" . $id . "\");'><strong>verwijderen</strong></a></td>";
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