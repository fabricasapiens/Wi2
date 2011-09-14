<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_1_3_2_ui extends Wi3_Plugin { 
        
        //UI requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_3_2_core");
        
        function __construct() {
            //register this Plugin and load dependencies
            parent::__construct();
            
            //load JQuery UI
            Wi3::$workplace->javascript('jquery/jquery-ui-1.7.2.custom.min.js'); 
            
            //load the UI css as well
            Wi3::$workplace->css("jquery_ui_flick/jquery-ui-1.7.2.custom.css");
            
        }
        
    }

?>