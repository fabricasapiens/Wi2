<?php

    class Wi3_pathof {
        
        public $wi3;
        public $site;
        public $sitetemplates;
        public $wi3templates;
        public $pagetemplate;
        public $pagefiller;
        
        public function __construct() {
            $this->wi3 = APPPATH;
            //site and pagefiller location can only be known after site has been loaded by Engine or View
            //we therefore register an hook on the wi3.siteandpageloaded event
            Event::add("wi3.siteloaded", array("Wi3_pathof", "fillSitePaths"));
            Event::add("wi3.pageloaded", array("Wi3_pathof", "fillPagePaths"));
            Event::add("wi3.siteandpageloaded", array("Wi3_pathof", "fillSiteAndPagePaths"));
        }
        
        public static function fillSiteAndPagePaths() {
            self::fillSitePaths();
            self::fillPagePaths();
        }
        
        public static function fillSitePaths() {
            Wi3::$pathof->site = APPPATH . "sites/" . Wi3::$site->url . "/";
        }
        
        public static function fillPagePaths() {
            Wi3::$pathof->pagetemplate = Wi3::$pathof->sitetemplates = Wi3::$pathof->wi3templates = Wi3::$pathof->site . "page_templates/";
            if (isset(Wi3::$page)) {
                Wi3::$pathof->pagefiller = APPPATH . "pagefillers/" . Wi3::$page->page_filler . "/";
            }
        }
        
        
    }

?>