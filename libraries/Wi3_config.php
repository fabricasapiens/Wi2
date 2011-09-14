<?php

    class Wi3_config {
        
        public function wi3($key) {
            return Kohana::config($key);
        }
        
        public function site($key) {
            //config of a site is located in the 'config' folder of a site
            //$key stands for the filename needs to be fetched
            //the config file is then included here, and will deliver a $config variable
            $config = array();
            if (file_exists(Wi3::$pathof->site . "config/" . $key . ".php")) {
                include(Wi3::$pathof->site . "config/" . $key . ".php");
            }
            return $config;
        }
        
    }

?>