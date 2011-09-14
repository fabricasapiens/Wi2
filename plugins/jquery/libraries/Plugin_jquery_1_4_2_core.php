<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_jquery_1_4_2_core extends Wi3_Plugin { 
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            //make JQuery work
            $this->javascript(array(
                //'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
                'jquery-1.4.2.min.js', //jquery core
            )); 
            
        }
        
    }

?>