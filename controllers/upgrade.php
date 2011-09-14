<?php

    class Upgrade_Controller extends Login_Controller {
        
        // Set the name of the template to use
        public $template = "wi3/workplace";
        
        public function __construct() {
            parent::__construct();
        }

        public function index() {
            $this->template->title = "Upgraden";
            $this->template->content = "<p>upgrade van <a href='" . Wi3::$urlof->wi3 . "upgrade/upgrade/1.0/2.0'>versie 1.0 naar versie 2.0</a></p>";
        }
        
        public function upgrade($from, $to) {
            $this->template->title = "Upgraden...";
            $this->template->content = "<h2>site '" . Wi3::$site->title .  "'</h2><p> opwaarderen van versie " . htmlentities($from) . " naar versie " . htmlentities($to) . ".</p>";
            //check all pages, and call the update function of their respective pagefillers (if such a function exists, that is)
            $pages = Wi3::$site->pages;
            ob_start();
            //make all existing users siteadmin (if there is not yet a siteadmin role, which would indiciate that an update already took place)
            $role = ORM::factory("role", "siteadmin");
            if (empty($role->id)) {
                //create role
                $role->name = "siteadmin";
                $role->save();
                echo "<p>siteadmin rol bestond nog niet en is aangemaakt.<br />";
                //add role to every user of the current site
                foreach(Wi3::$site->users as $user) {
                    $user->add($role);
                    $user->save();
                    echo "siteadmin rol toegekend aan " . $user->username . ".<br />";
                }
                echo "</p>";
            }
            echo "<p>opwaarderen van " . count($pages) . " pagina's.</p><p>";
            foreach($pages as $page) {
                //ok, now load the pagefiller that controls how to display and edit this page
                if (isset($page->page_filler) AND empty($page->page_filler)) { $page->page_filler = "default"; $page->save(); }
                //now load the pagefiller
                echo "opwaarderen van pagina " . $page->id . " (" . $page->title . ")...<br />";
                if  (isset($page->page_filler)) {
                    $libname = "Pagefiller_" . strtolower($page->page_filler);
                    $pagefiller = new $libname();
                    if (method_exists($pagefiller, "event")) {
                        $pagefiller->event("upgrade", array("page" => $page, "from" => $from, "to" => $to));
                    }
                }
            }
            echo "</p>";
            $content = ob_get_contents();
            $this->template->content .= $content;
            ob_end_clean();
        }
        
        //this functions makes sure that any views are loaded in the Wi3::$workplace namespace
        //so the $this in these views refers to Wi3::$workplace
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            //we want the pagetemplate (and other templates) to be available through the Wi3_workplace namespace
            return Wi3::$workplace->_kohana_load_view($kohana_view_filename, $kohana_input_data);
        }
        
    }
    
?>