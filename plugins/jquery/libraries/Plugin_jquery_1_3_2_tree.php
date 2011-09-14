<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_1_3_2_tree extends Wi3_Plugin { 
        
        //requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_3_2_core");
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            Wi3::$workplace->javascript("jquery/jquery.simple.tree.js"); //for tree displaying plus drag&drop
            
        }
        
    }

?>