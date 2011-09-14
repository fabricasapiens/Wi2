<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php if (isset($title)) { echo html::specialchars($title); } ?></title>
    
    <?php 
        //enable caching on this whole page
        $this->cacheable = false; 
    ?>
    
    <?php $this->css("reset.css"); ?>
    <?php $this->css("default.css"); ?>
    
    <?php $this->javascript("test.js"); ?>

</head>
<body>

    <div id="wrap">
    
        <div id='title'>
            <?php echo $site->title; ?>
        </div>
        
        <div id='menu'>
        <?php
            
            //proper navigation-rendering
            echo $this->menu($pages, $page);
            
        ?>
        </div>
        
        <div id='pagetitle'>
            <?php echo $page->title; ?>
        </div>
    
        <div id='contentwrap'>
            <div class='dropzone' id='secondaryContent'>
            <?php
            
                //test een static field invoegen 
                $veld = Pagefiller_default::factory("siteField", "dtest", array("type" => "component_text"));
                echo "<div class='wi3_siteField' id='wi3_siteField_" . $veld->id . "'>";
                echo $veld->render();
                echo "</div>";
            
            ?>
            &nbsp</div>
            <div class='dropzone' id='mainContent'>
            <?php if (isset($mainContent)) {
                    echo $mainContent;
                }
            ?>
            &nbsp</div>
        </div>

    </div>

</body>
</html>
