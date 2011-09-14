<?php

    class Pagefiller_default_component { 
    
        //caching of the field that this component handles is by default not allowed
        public $cacheable = false;
        
    
        static $__is_instantiated = false;
        public static function __is_instantiated() {
            return self::$__is_instantiated;
        }
        
        //checking for dependencies
        //these dependencies can be other components as well as plugins
        public function __construct() {
            //make visible that there now is an instance of this class (so that if classes depend on any instance of this class, they will know it's all right)
            self::$__is_instantiated = true;
            //now check for dependencies and create a class if necessary
            //we should not just create new objects for every dependency, because 
            //a. that consumes more memory
            //b. can cause unexpected repetitive behaviour when that class/component does anything 'global' (like adding a CSS file) in their constructor
            //first, check the component dependencies
            if (isset($this->wi3_dependencies_components)) {
                foreach($this->wi3_dependencies_components as $dep) {
                    if (class_exists($dep)) {
                        //ok, so we can check the class for instantiation
                        //$instantiated = $dep::__is_instantiated();
                        eval("$instantiated = " . $dep . "::$__is_instantiated"); //ANY SOLUTION TO THIS? WITHOUT A HEBREW ERROR?
                        if ($instantiated == false) {
                            Pagefiller_default::factory("component", $dep); //instantiate an object. This object will in turn check its dependencies too, making this recursive. Note: circle-dependencies are ABSOLUTELY forbidden ;)
                        }
                    } else {
                        Kohana::log("error", "Component dependency " . $dep . " for component " . get_class($this) . " does not exist.");
                    }
                }
            }
            
            //now do the plugin dependencies
            if (isset($this->wi3_dependencies_plugins)) {
                foreach($this->wi3_dependencies_plugins as $dep) {
                    Wi3::$plugins->load($dep); //load up the required plugins
                }
            }
            
        }
        
        //-------------------------------------------------------------
        // these functions are used to include static content from the Wi3/static folder
        //-------------------------------------------------------------
        public function css($file, $category = "component") {
            if (is_array($file)) {
                foreach($file as $f) { 
                    Css::add(Wi3::$urlof->pagefiller . "components/" . strtolower(get_class($this)) . "/static/css/" . $f, $category);
                }
            } else {
                Css::add(Wi3::$urlof->pagefiller ."components/" . strtolower(get_class($this)) . "/static/css/" . $file, $category);
            }
        }
        
        public function javascript($file, $category = "component") {
           if (is_array($file)) {
                foreach($file as $f) { 
                   Javascript::add(Wi3::$urlof->pagefiller. "components/" . strtolower(get_class($this)) . "/static/javascript/" . $f, $category);
                }
            } else {
                Javascript::add(Wi3::$urlof->pagefiller . "components/" . strtolower(get_class($this)) . "/static/javascript/" . $file, $category);
            }
        }    
        
        public function view($viewname) {
            return ExternalView::factory(Wi3::$pathof->pagefiller . "components/" . strtolower(get_class($this)) . "/views/" . $viewname . ".php");
        }
    
    }

?>