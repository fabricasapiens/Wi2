<?php

    class Wi3_plugins {
        
        //stores all instances of the plugins
        public $plugins = Array();
        
        //----------------------------
        // method for registering plugins, as available through Wi3::$plugins
        //----------------------------
        public function register($pluginname, $class = "") {
            $pluginname = ucfirst($pluginname);
            if (empty($class)) { $class = $pluginname; }
            if (is_string($class)) {
                $class = ucfirst($class);
                //create an instance if a string (classname) is provided
                //by creating the class, it will register itself if it is extending Wi3_Plugin
                new $class();
            } else {
                //add the plugin to the plugins array
                $this->plugins[$pluginname] = $class;
            }
        }
        
        //----------------------------
        // function to register a plugin if it is not already registered
        //----------------------------
        public function registeronce($pluginname, $class = "") {
           if (!isset($this->plugins[ucfirst($pluginname)])) {
                $this->register($pluginname, $class);
            }
        }
        
        //----------------------------
        // function to 'require' a plugin, so to make it loaded
        //----------------------------
        public function load($pluginname) {
            $this->registeronce($pluginname);
        }
        
        //----------------------------
        // magic methods for retrieving and setting plugins
        //----------------------------
        public function __GET($pluginname) {
            //is called when someone wants to have the plugin
            //return the instance of the plugin from our plugins-array
            //first, create an instance if there is not already one
            $pluginname = ucfirst($pluginname);
            $this->registeronce($pluginname, $pluginname);
            return $this->plugins[$pluginname];
        }
        
        public function __SET($pluginname, $val) {
            //well, this probably does not happen that often, but is supported anyway
            return $plugins[$pluginname] = $val;
        }
        
    }

?>