<?php

    Class Wi3 {
        
        //Wi3 class handles only up to the site and page
        //It does NOT handle fields, field components, or anything 'in a page'
        //Wi3 is only a kickstart class that then passes a page towards its pagefiller, which then handles the rest
        
        public static $user;    //when user is logged in
        
        public static $site;        //always set
        public static $page;     //set when dealing with a page (that is almost always, except in the wi3 workplace pages itself)
        
        public static $editmode;    //whether we are in edit-mode or in view mode
        
        //different departments of Wi3
        // --- departments that are used primarily from within wi3 'workplace'. They do not assume the editing or viewing of a certain page (that is handled by pagefillers)
        public static $routing;
        public static $workplace;  //wokrplace specific functions, also to include javascript, css and images from the APP / static folder
        public static $pages;  //functions to add/move/delete pages
        public static $sites; //functions to add/remove sites or edit properties of an existing site
        public static $rights;  //functions to check rights
        // --- departments that are used by both the workplace and components/sites
        public static $urlof;
        public static $pathof;
        public static $config;  //to fetch the config files from both sites and the wi3 application
        // --- departments that are used primarily from components/pagefillers/pagetemplates
        public static $files;
        public static $cache;
        public static $nav;
        public static $template;    //functions and vars that templates can use in their namespace
        public static $component;   //functions and vars that a component can use in its namespace
        public static $pagefiller;   //functions and vars that a pagefiller can use in its namespace
        
        // --- this is the plugins object, where plugins should register with
        // this is done by invoking a hook to the "wi3.registerplugins" event and then execute the static "register" method of your pluging "PluginClass"
        // 
        public static $plugins;
        
        //the array that describes the departments
        //one could alter departments to this via a hook into the Wi3.beforesetup Event
        //to *add* departments, one should use the plugins possibility
        public static $departmentclasses = array(
            "routing" => "Wi3_routing",
            "workplace" => "Wi3_workplace",
            "pages" => "Wi3_pages",
            "rights" => "Wi3_rights",
            "urlof" => "Wi3_urlof",
            "pathof" => "Wi3_pathof",
            "files" => "Wi3_files",
            "cache" => "Wi3_cache",
            "nav" => "Wi3_nav",
            "template" => "Wi3_template",
            "pagefiller" => "Wi3_pagefiller",
            "config" => "Wi3_config"
        );
        
        //----------------------------------------
        // setup is called after routing of Kohana. This is achieved in the hook 'wi3_setup' by registering into the "system.pre_controller" event
        //----------------------------------------
        public static function setup() {
            Event::run("wi3.beforesetup");
            
            //setup the Wi3 departments: Wi3_routing, Wi3_files, Wi3_cache, Wi3_urlof and Wi3_pathof and the "otherdepartments" array
            foreach(self::$departmentclasses as $dep => $class) {
                self::$$dep = new $class();
            }
            
            //now that the basic departments have been initialized, go ahead and let the plugins register themselves
            self::$plugins = new Wi3_plugins(); //create Plugins object that handles the access to the plugins
            Event::run("wi3.registerplugins");
            
            Event::run("wi3.aftersetup");
        }
        
        //----------------------------------------
        // factory function to create instances of sites, pages, pagefillers, etc
        //----------------------------------------
        public function factory($type, $id = "") {
            
        }
        
        //----------------------------------------
        // small helper functions
        //----------------------------------------
        public function date_now($length=20) {
            return substr(date("YmdHis") . (microtime()*1000000) , 0, $length);
        }
        
        public function optionlist($list, $selectedval) {
            $ret = "";
            //watch out, (string)false == (string)"" !!!
            foreach($list as $val => $label) {
                $ret .= "<option value='" . $val . "' " . ((!empty($selectedval) AND !empty($val) AND (string)$val == (string)$selectedval) ? "selected='selected'" : "") . ">" . $label . "</option>";
            }
            return $ret;
        }
        
    }
    
?>
