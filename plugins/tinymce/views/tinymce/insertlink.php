<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <link rel="stylesheet" href="<?php echo url::base(FALSE) . "media/css/reset.css"; ?>" type="text/css" media="all">
        <link rel="stylesheet" href="<?php echo url::base(FALSE) . "media/css/style.css"; ?>" type="text/css" media="all">
        <style>
            body { padding: 20px; }
            #images { overflow: auto; height: 300px;}
            .image { height: 100px; width: 100px; }
            .imageclear { position: relative; clear: both; visibility: hidden; }
            #imagebutton { display: none; }
        </style>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php if (isset($title)) { echo html::specialchars($title); } ?></title>
        <script>
        
            function selectFile(div) {
                var link = div.title;
                submitFile(link);
            }
            
            function selectURL() {
                var link = document.getElementById("custom_link_input").value;
                submitFile(link);
            }
        
            function submitFile(link) {
        
                var t = this;
            
                var w = window.dialogArguments || opener || parent || top;
                tinymce = w.tinymce;
                tinyMCE = w.tinyMCE;
                t.editor = tinymce.EditorManager.activeEditor;
                //t.params = t.editor.windowManager.params;
                //t.features = t.editor.windowManager.features;
                ed = t.editor;
                
                args = { href: link, title: link};
                var newA = ed.dom.create('a', args, ed.selection.getContent());
                
                
                ed.selection.setContent(ed.dom.getOuterHTML(newA));
                
                this.close();
                
            }
        
        </script>
    </head>
    <body>
        <div id='custom_link'>
            <p>Geef de locatie op waarnaar een link ingevoegd moet worden</p>
            <form onSubmit='selectURL()'><input id='custom_link_input' value='http://'/> <input type='submit' value='invoegen'/></form>
        </div>
        <div id='images'>
        <?php

            if (count($files) == 0) {
                echo "Er zijn geen bestanden beschikbaar.";
            } else {
                echo "<p>Of selecteer het bestand waarvoor een link ingevoegd moet worden.</p><p>";
                $counter = 0;
                foreach($files as $file) {
                    if ($file->type != "folder") {
                        echo "<div class='imagediv'><a href='javascript:void(0)' onClick='selectFile(this);' title='" . Wi3::$urlof->file($file->filename) . "'>" . $file->filename . "</a></div>";
                    }
                }
                echo "</p>";
            }

        ?>
        </div>
        <p>
            <br />
            <a href='javascript:void(0);' onClick="window.close();">venster sluiten</a>
        </p>
    </body>
</html>