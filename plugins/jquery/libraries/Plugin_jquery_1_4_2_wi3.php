<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_1_4_2_wi3 extends Wi3_Plugin { 
        
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_4_2_core", "Plugin_clientjavascriptvars");
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            Wi3::$workplace->javascript(array(
                    'wi3.js', //enables certain basic Javascript functions like clientside client<>server communication, tinymce initialization and popups
            )); 
            
        }
        
    }

?>