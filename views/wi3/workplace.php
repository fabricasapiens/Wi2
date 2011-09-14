<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if (isset($title)) { echo html::specialchars($title); } ?></title>

    <?php

    $this->css("reset.css");
    $this->css("style.css");
    
    //load some JQuery plugins
    Wi3::$plugins->load("plugin_jquery_1_3_2_core");
    Wi3::$plugins->load("plugin_jquery_1_3_2_tree");
    Wi3::$plugins->load("plugin_jquery_1_3_2_wi3");
    
    //load the client Javascript information plugin
    Wi3::$plugins->load("plugin_clientjavascriptvars");
    
   $this->javascript(array(     
        'workplace.js', //creates the hovers, dragdropping etc in the 'menu' page and contains the Iframe-functions
    )); 
    
    ?>

</head>
<body>

    <div id='container'>
    
        <div id='navigation'>
        
            <?php 
                //subnavigationleft, if set
                if (isset($navigationleft)) { echo "<div id='navigationleft'>" .$navigationleft."</div>"; } 
            ?>
            
            <?php 
                //subnavigationright, if set
                if (isset($navigationright)) { echo "<div id='navigationright'>" .$navigationright."</div>"; } 
            ?>
            
        </div>
        
        <?php if (isset($totalcontent)) { echo $totalcontent; } ?>

        <div id='content' <?php if (isset($contentclass)) { echo "class='" . $contentclass . "'"; } ?>>
            <?php if (isset($title)) { echo "<h1>" . html::specialchars($title) . "</h1>"; } ?>
            <?php if (isset($content)) { echo $content; } ?>
           
            <p id="copyright">
                Copyright ©2007–2010 Fabrica Sapiens
            </p>
        
        </div>
        
    </div>

</body>
</html>