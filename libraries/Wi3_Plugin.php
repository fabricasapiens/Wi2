<?php

    //----------------------------
    // This class should be extended by Plugins and will automagically register any Plugin when it is instanciated
    // and will make sure there is only one instance of any Plugin at any time
    //----------------------------
    abstract class Wi3_plugin {
        
       function __construct() {
            
            //check dependencies
            //now do the plugin dependencies
            if (isset($this->wi3_dependencies_plugins) AND is_array($this->wi3_dependencies_plugins)) {
                foreach($this->wi3_dependencies_plugins as $dep) {
                    Wi3::$plugins->registeronce($dep);
                }
            }
            
            $class = get_class($this);
            Wi3::$plugins->registeronce($class, $this); 
            return Wi3::$plugins->$class;
        }
        
        //-------------------------------------------------------------
        // these functions are used to include static content from the plugins/[someplugin]/static folder
        //-------------------------------------------------------------
        public function css($file, $category = "wi3") {
            //find the directory of this plugin
            $location = Kohana::find_file("libraries", get_class($this));
            //extract the 'base' directory
            $directory = substr($location, 0, strrpos($location, "/libraries/")) . "/";
            //now remove the first part of the dir, so that the remaining dir works from the wi3 base dir
            $directory = substr($directory, strlen(Wi3::$pathof->wi3));
            //at last, create an URL to the wi3 base dir and add the remaining dir
            $url = Wi3::$urlof->wi3 . $directory;
            if (is_array($file)) {
                foreach($file as $f) { 
                    Css::add($url . "static/css/" . $f, $category);
                }
            } else {
                Css::add($url . "static/css/" . $file, $category);
            }
        }
        
        public function javascript($file, $category = "wi3") {
            ///find the directory of this plugin
            $location = Kohana::find_file("libraries", get_class($this));
            //extract the 'base' directory
            $directory = substr($location, 0, strrpos($location, "/libraries/")) . "/";
            //now remove the first part of the dir, so that the remaining dir works from the wi3 base dir
            $directory = substr($directory, strlen(Wi3::$pathof->wi3));
            //at last, create an URL to the wi3 base dir and add the remaining dir
            $url = Wi3::$urlof->wi3 . $directory;
            if (is_array($file)) {
                foreach($file as $f) { 
                   Javascript::add($url. "static/javascript/" . $f, $category);
                }
            } else {
                Javascript::add($url . "static/javascript/" . $file, $category);
            }
        }    
        
    }

?>