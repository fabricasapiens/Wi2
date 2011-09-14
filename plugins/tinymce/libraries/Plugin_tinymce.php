<?php

    Class Plugin_tinymce extends Wi3_Plugin {
        
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            //enables Tinymce
            Wi3::$workplace->javascript(array(
                'tinymce/tiny_mce.js' //TinyMCE
            )); 
        }
        
    }

?>