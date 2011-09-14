<?php

    //to view a protected file of one of the sites, the user most often needs to be logged in (except when the file does not require any rights to be viewed, but then again it is useless to place it in the protected folder)
    class SiteResourceLoader_Controller extends LoginIfPossible_Controller {
      
        public $template = "wi3/empty";
      
        public function get($filename) {
            //first get the sitename (of the site the user wants the file from) by extracting it from the url
            $originalurl = $_GET["url"];
            preg_match("@/sites/([^/]+)/data/files/protected/@", $originalurl, $matches);
            $sitename = $matches[1];
            if (!empty($sitename)) {
                //get site
                $site = ORM::factory("site", $sitename);
                Wi3::$site = $site;
                Event::run("wi3.siteloaded");
                //now, get the file that we are looking for
                $file = ORM::factory("file")->where("site_id", $site->id)->where("filename", $filename)->find();
                //and finally, check if the user is allowed to load it
                if (Wi3::$rights->check("view", $file) == true AND file_exists(Wi3::$pathof->site . "data/files/protected/" . $filename)) {
                    header('Content-type: image');
                    readfile(Wi3::$pathof->site . "data/files/protected/" . $filename);
                } else {
                    //not allowed to view this file OR it does not exist. Return 404
                    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
                    die();
                }
            }
          
        }
        
    }

?>
