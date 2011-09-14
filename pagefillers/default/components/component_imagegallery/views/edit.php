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
        <h2>Kies een map waaruit afbeeldingen weergegeven moeten worden</h2>
        <?php
        
            $file = Wi3::$files->findRecursive(Array("countFileDescendants" => true, "returnRoot" => true));
            $currentid = $field->get("folderid");
            
            function recursive($files, $currentid) {
                echo "<ul>";
                foreach($files as $file) {
                    if ($file->type == "folder") {
                        echo "<li " . ($file->id == $currentid ? "style='font-weight: bold;'" : "") . " id='component_imagegallery_edit_li_" . $file->id . "'><a href='javascript:void(0);' onClick='wi3.pagefiller.components.component_imagegallery.selectfolder(" . $file->id . ");'>" . $file->filename . "</a> (" . $file->fileDescendantsAmount . ")";
                        if (!empty($file->children)) {
                            recursive($file->children, $currentid);
                        }
                        echo "</li>";
                    }
                }
                echo "</ul>";
            }
            echo "<ul><li " . ($file->id == $currentid ? "style='font-weight: bold;'" : "") . " id='component_imagegallery_edit_li_0'><a href='javascript:void(0);' onClick='wi3.pagefiller.components.component_imagegallery.selectfolder(" . $file->id . ");'>Alle afbeeldingen</a> (" . $file->fileDescendantsAmount . ")";
                recursive($file->children, $currentid);
            echo "</li></ul>";
        
        ?>
        
    </div>
    <div id="<?php echo $prefix; ?>_edit_tabs-2">
        <?php
        
           echo "<h2>Bestaande elementen</h2>";
            
        ?>
    </div>
</div>