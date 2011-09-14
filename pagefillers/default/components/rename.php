<?php
    
    foreach (new DirectoryIterator('.') as $fileInfo) {
        if($fileInfo->isDot()) continue;
        if(!$fileInfo->isDir()) continue;
        echo "<a href='?map=" . $fileInfo->getFilename() . "'>" . $fileInfo->getFilename() . "</a><br>\n";
    }
    
    if (isset($_GET["map"]) AND !empty($_GET["map"])) {
        echo "<h1>Replacement actions</h1>";
        //rename everything that mentions 'component_example' or 'example' and replace it with the new component-name (thus, the foldername 'map')
        replaceInDir($_GET["map"]);
    }
    
    function replaceInDir($dirname) {
        echo "DIRECTORY processing " . $dirname . "<br />";
        foreach (new DirectoryIterator($dirname) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            //for every folder, go recursively
            if($fileInfo->isDir()) {
                replaceInDir($fileInfo->getPathName());
                continue;
            }
            //for every file, go into the file and replace the content, then change the filename itself
            $content = file_get_contents($fileInfo->getPathName());
            $newcontent = preg_replace(array("@component_example@", "@Component_example@", "@(?<![cC]omponent_)example@"),  array($_GET["map"], ucfirst($_GET["map"]), $_GET["map"]) , $content,-1,$replacements);
            if ($replacements > 0) {
                echo "FILE CONTENT of " . $fileInfo->getFilename() . " had " . $replacements . " replacements in its content.<br />";
                file_put_contents($fileInfo->getPathName(), $newcontent);
            }
            //now rename the file
            $newfilename = preg_replace(array("@component_example@", "@Component_example@"),  array($_GET["map"], ucfirst($_GET["map"])), $fileInfo->getFilename());
            if ($newfilename != $fileInfo->getFilename()) {
                rename($fileInfo->getPathName(), dirname($fileInfo->getPathName()) . "/" . $newfilename);
                echo "FILE " . $fileInfo->getFilename() . " is processed and renamed to " . $newfilename . "<br />";
            }
        }
    }

?>