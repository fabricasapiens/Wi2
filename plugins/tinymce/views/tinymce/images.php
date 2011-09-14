<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php 
            Wi3::$workplace->css("reset.css");
            Wi3::$workplace->css("style.css");
        ?>
        <style>
            html body { padding: 20px; }
            #images { overflow: auto; height: 300px; }
            /*.imagediv { width: 100px; padding: 10px;  border: 2px solid #ccc; margin: 10px; margin-left: 0px; float: left; }*/
            .imagediv { width: 100px; height: 100px; margin: 5px; float: left; }
            .image { height: 100px; width: 100px; }
            .imageclear { position: relative; clear: both; visibility: hidden; }
            #imagebutton { display: none; }
        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php if (isset($title)) { echo html::specialchars($title); } ?></title>
        <script>
        
            function selectImage(imagediv) {
                document.imagediv = imagediv;
                document.getElementById("imagebutton").innerHTML = "Afbeelding " + imagediv.nextSibling.innerHTML + " invoegen."
                document.getElementById("imagebutton").style.display = "inline";
            }
        
            function submitImage() {
        
                var t = this;
            
                var w = window.dialogArguments || opener || parent || top;
                tinymce = w.tinymce;
                tinyMCE = w.tinyMCE;
                t.editor = tinymce.EditorManager.activeEditor;
                //t.params = t.editor.windowManager.params;
                //t.features = t.editor.windowManager.features;
                
                args = {};
                image = document.imagediv.firstChild;
                var size = document.getElementById("imagesize").value;
                var src = image.src.replace("100", size);
                tinymce.extend(args, {
                    src : src,
                    title : image.title,
                    style : "margin: 10px; float: left; margin-left: 0px; margin-top: 0px;",
                    id : image.id
                });
                
                ed = t.editor;
                el = ed.selection.getNode();
                if (el && el.nodeName == 'IMG') {
                    ed.dom.setAttribs(el, args);
                } else {
                    ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" />', {skip_undo : 1});
                    ed.dom.setAttribs('__mce_tmp', args);
                    ed.dom.setAttrib('__mce_tmp', 'id', '');
                    ed.undoManager.add();
                }
                
                this.close();
                
            }
        
        </script>
    </head>
    <body>
        <div id='images'>
        <?php
        
            Wi3::$urlof->fillsiteandpagefiller();

            if (count($images) == 0) {
                echo "Er zijn geen afbeeldingen beschikbaar.";
            } else {
                echo "Selecteer de afbeelding die ingevoegd moet worden.<br />";
                $counter = 0;
                foreach($images as $image) {
                    
                    $url = $image->url;
                    $counter++;
                    
                    $filenamepos = strrpos($url, "/")+1;
                    $filename = substr($url, $filenamepos);
                    $dir = substr($url, 0, $filenamepos);
                  
                    echo "<div class='imagediv'><div class='image' onClick='selectImage(this);'>" . html::image(Wi3::$urlof->image($filename, 100)) . "</div><div style='display: none;'>" . $counter . "</div></div>";
                }
                echo "<div class='imageclear'>.</div>";
            }

        ?>
        </div>
        <?php
            echo "Breedte: <select id='imagesize'><option value='50'>50 pixels</option><option value='100'>100 pixels</option><option value='150'>150 pixels</option><option value='200'>200 pixels</option><option value='250'>250 pixels</option><option value='300'>300 pixels</option><option value='350'>350 pixels</option><option value='400'>400 pixels</option><option value='450'>450 pixels</option><option value='500'>500 pixels</option><option value='550'>550 pixels</option><option value='600'>600 pixels</option><option value='650'>650 pixels</option><option value='700'>700 pixels</option><option value='750'>750 pixels</option><option value='800'>800 pixels</option><option value='850'>850 pixels</option><option value='900'>900 pixels</option><option value='950'>950 pixels</option><option value='1000'>1000 pixels</option></select> ";
            echo '<button id="imagebutton" onClick="submitImage()">afbeelding invoegen</button>';
        ?>
        <p>
            <br />
            <a href='javascript:void(0);' onClick="window.close();">venster sluiten</a>
        </p>
    </body>
</html>