<?php

    //no need to be logged in with this controller, but if it is possible to login (ie, the user has already logged in earlier and still has the session)
    //then just login the user. Do not force him however. Thus, extend with LoginIfPossible Controller
    class View_Controller extends LoginIfPossible_Controller {
      
        public $template = "wi3/workplace";
      
        public function index() {
            return $this->notfound();
        }
        
        public function notfound() {
            $this->template = "wi3/empty";
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            //return main page
            die(file_get_contents(Wi3::$urlof->site));
        }
        
        public function site($sitename = "", $pagename = "") {
            //this function renders a user-site
            //it is meant to be called from the /wi3/sites folder, which redirects here via .htaccess
            
            //set the Wi3::$editmode property to false. The user is not editing, just viewing
            Wi3::$editmode = false;
            
            //if there's no site, show 404
            if (empty($sitename)) {
                return $this->notfound();
            } else {
                
                //load site
                //$site = ORM::factory("site")->with("pages")->where("url", $sitename)->find(); //site-models can also be loaded by url
                $site = ORM::factory("site", $sitename);
                Wi3::$site = $site;
                $modules = $site->modules; //always returns an Array
                
                //now try to load the site-specific database configuration
                Event::run("wi3.siteloaded"); //this will induce the $pathof lib to fill the paths to the site
                if (file_exists(Wi3::$pathof->site . "config/database.php")) {
                    // Load database configuration
                    function _loaddbconfig($sitename) {
                        // load the database config of this site (if it exists in the database-config-file)
                        // the config-name NEEDS to be "wi3_[sitename]", or otherwise it will not be loaded here
                        include(Wi3::$pathof->site . "config/database.php");
                        $sitedbconfig = $config["wi3_" . $sitename];
                        // Call the database and ask it to create an instance 'sitespecificconfig' from the config array
                        // That instance will also be saved to the Database instances list, so all next calls for 'sitespecificconfig' will instantly be loaded from memory
                        $load = Database::instance("sitespecificconfig", $sitedbconfig);
                        // The ORM models that use the sitespecific configuration (if available, that is), will have $_db = 'sitespecificconfig' in their model description
                        // These models are the user, role, role_user, page, file
                        // Automatically, when this sitespecific configuration is not available, Kohana will load the default config               
                    }
                    _loaddbconfig($sitename); // Execute within function so that variable namespace does not get polluted
                }
                
                //fetch page
                $page = Wi3::$pages->get_page($pagename);
                
                // Redirects
                // Note: these do NOT work when editing the site.
                // Thus, it is possible to edit a page while temporarely redirecting to another page for viewers
                //check for redirects
                if (isset($page->page_redirect_type) AND !empty($page->page_redirect_type) AND $page->page_redirect_type != "none") {
                    //fetch redirect
                    if ($page->page_redirect_type == "wi3") {
                        //echo Wi3::$urlof->site . "/" . $page->page_redirect_type;
                        // simply load the page we want to redirect to...
                        $page = Wi3::$pages->get_page($page->page_redirect_wi3);
                        Event::run("wi3.siteandpageloaded");
                        //die(file_get_contents(Wi3::$urlof->site . $page->page_redirect_wi3));
                    } elseif ($page->page_redirect_type == "external") {
                        $redirectexternal = true; // Redirect will occur after rights check below
                    }
                }
                Wi3::$page = $page;
                
                //check whether this page can be view by the current viewer
                $allowed = Wi3::$rights->check("view", $page);
                if ($allowed == false) {
                    //do as if this page does not exist
                    Event::run("wi3.siteandpageloaded");
                    return $this->notfound();
                }
                
                // Do the redirect, now that we know that this is allowed
                if (isset($redirectexternal))
                {
                    // send headers for redirect
                    header("HTTP/1.1 303 See Other");
                    header("Location: " . $page->page_redirect_external);
                    die();
                }
                
                //ok, now load the pagefiller that controls how to display and edit this page
                if (empty($page->page_filler)) { $page->page_filler = "default"; $page->save(); }
                
                //now run the event that page and site are loaded
                //Wi3_pathof hooks into this to fetch the path of the current site and of the pagefiller
                Event::run("wi3.siteandpageloaded");
                
                $libname = "Pagefiller_" . strtolower($page->page_filler);
                $pagefiller = new $libname();
                $this->template = $pagefiller->view_page($site, $page); //a pagefiller should return a View object
                //done
                //return the View object
                return $this->template;
                
            }
        } //end of view function
        
        //this functions makes sure that any views are loaded in the Wi3::$template namespace
        //so the $this in these views refers to Wi3::$template
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            if (Wi3::$routing->action == "site") {
                //we want the pagetemplate (and other templates) to be available through the Wi3_template namespace
                return Wi3::$template->_kohana_load_view($kohana_view_filename, $kohana_input_data);
            } else {
                return parent::_kohana_load_view($kohana_view_filename, $kohana_input_data);
            }
        }
        
    }

?>
