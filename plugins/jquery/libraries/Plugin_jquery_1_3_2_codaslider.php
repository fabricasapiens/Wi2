<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_1_3_2_codaslider extends Wi3_Plugin { 
        
        //UI requires core JQuery
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_3_2_core", "Plugin_jquery_1_3_2_easing");
        
        function __construct() {
            //register this Plugin and load dependencies
            parent::__construct();
            
            //load the codaslider css
            $this->css("coda-slider-2.0");
            
            //load the javascript
            $this->javascript("jquery.coda-slider-2.0.js");
            
        }
        
    }

?>