<?php 
    //set component type and ID as prefix for element ID's
    $prefix = $field->type; 
?>
<div id="<?php echo $prefix; ?>_edit_tabs">
	<ul>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-1">Product toevoegen</a></li>
		<li><a href="#<?php echo $prefix; ?>_edit_tabs-2">Producten wijzigen/verwijderen</a></li>
	</ul>
	<div id="<?php echo $prefix; ?>_edit_tabs-1">

        <?php //the button to add an element ?>
        <h2>Toe te voegen product</h2>
        <p><span style='visibility:hidden;'>a</a></p>
        <p>
                <form id='<?php echo $prefix; ?>_add' onSubmit='return wi3.pagefiller.components.<?php echo $prefix; ?>.add();'><table>
                <tr><td> naam </td><td> <input name='name' /></td></tr>
                <tr><td> prijs </td><td> <input style='width: 100px;' name='price_euro' /> euro en <input style='width: 100px;' name='price_eurocent' /> eurocent</td></tr>
                <?php
                    //<tr><td> datum </td><td> <input name='date' class='componentInput_datepicker' /></td><tr>
                ?>
                <?php
                    // Create componentInput of type 'image' and using a newly created input element named 'image'
                    $componentInputImage = Pagefiller_default_componentinput::factory("image", "image");
                    echo "<tr><td> foto </td><td>" . $componentInputImage->render() . "</td></tr>";
                ?>
                <tr><td> tekst </td><td> <textarea name='text' class='componentInput_tinyMCE' /></td></tr>
                <tr><td><span style='visibility:hidden;'>a</a></td></tr>
                <tr><td> </td><td><button onClick='return wi3.pagefiller.components.<?php echo $prefix; ?>.add();'>product toevoegen</button></td></tr>
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
                <tr><td> naam </td><td> <input name='name' /></td></tr>
                <tr><td> prijs </td><td> <input style='width: 100px;' name='price_euro' /> euro en <input style='width: 100px;' name='price_eurocent' /> eurocent</td></tr>
                <?php
                    //<tr><td> datum </td><td> <input name='date' class='componentInput_datepicker' /></td><tr>
                ?>
                <?php
                    // Create componentInput of type 'image' and using a newly created input element named 'image'
                    $componentInputImage = Pagefiller_default_componentinput::factory("image", "image");
                    echo "<tr><td> foto </td><td>" . $componentInputImage->render() . "</td></tr>";
                ?>
                <tr><td> tekst </td><td> <textarea name='text' class='componentInput_tinyMCE' /></td></tr>
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