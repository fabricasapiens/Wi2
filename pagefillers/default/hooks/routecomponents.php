<?php

class Routecomponents {
    
    public static $originalmodulepaths = array(); //the original modules paths that Kohana looks for to find files
    
    //include all the component-controller folders in the Kohana modules search path
    public function route(){
        //backup the original paths array
        $modulepaths = self::$originalmodulepaths = Kohana::config("core.modules");
        
        //loop through all component folders
        foreach (new DirectoryIterator(APPPATH . "pagefillers/default/components") as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $modulepaths[] = APPPATH . "pagefillers/default/components/" . $fileInfo->getFilename();
        }
        
        //save module paths to configuration
        Kohana::config_set("core.modules", $modulepaths);
        
        //after the routing, the original modulepaths will be restored with the restorepaths, which is called at the sytsem.routing event, after the Router Setup has run
    }
    
    //now, restore the original Kohana search path, so that the component controllres are included with routing, but the rest (views/libs) is not
    public function restorepaths() {
        //save original module paths to configuration
        Kohana::config_set("core.modules", self::$originalmodulepaths);
    }
}

//add this whole to the system.routing Event so that the component controller paths will be included BEFORE routing is done
//and that the paths will be restored to normal AFTER routing is done
Event::add_after('system.routing', array('Router', 'find_uri'), array('Routecomponents', 'route'));
Event::add_after('system.routing', array('Router', 'setup'), array('Routecomponents', 'restorepaths'));

?>