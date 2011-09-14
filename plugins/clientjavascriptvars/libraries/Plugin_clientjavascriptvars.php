<?php
    
    //extending with Wi3_Plugin makes the plugin register itself the very moment an instance is created
    Class Plugin_clientjavascriptvars extends Wi3_Plugin { 
        
        function __construct() {
            //register this Plugin
            parent::__construct();
            
            //-------------------------------------------------------------
            // this function ensures that some Wi3 information is always sent to the client for use in javascript files (ie kohana.js)
            //
            //set up the event to pass information to the clientside
            //add this event before the javascript event, so that javascript always have this information available when they load
            Event::add_before('system.display', array('Javascript','render_in_head'), array("Plugin_clientjavascriptvars", "addclientjavascriptvars") );
            //-------------------------------------------------------------
            
        }
        
        //-------------------------------------------------------------
        // this function adds javascript client variables in the head of the page
        // is called from the System.display event, as set in the Wi3::setup
        //-------------------------------------------------------------
        public static function addclientjavascriptvars() {
            $information = array( 
                "routing" => Array(
                    "controller" => Wi3::$routing->controller,
                    "action" => Wi3::$routing->action,
                ),
                "urlof" => Array(
                    "wi3" => Wi3::$urlof->wi3,
                    "site" => Wi3::$urlof->site,
                ),
                "editmode" => Wi3::$editmode,
            );
            Event::$data = str_replace("</head>", "<script> var wi3 = " . json_encode($information) . "</script></head>", Event::$data);
        }
        
    }

?>