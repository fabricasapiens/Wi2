<?php

    class Wi3_urlof {
        
        public $request;    //current url, same as Wi3::$routing->url
        
        public $wi3;    //url to current wi3 instance... this is a 'full' url, it will include the full path to wi3
        public $site;   //base-url to the site. This url is as short as possible, so if there's a redirect from http://domain.com/page, this $site will be http://domain.com/
        public $page;   //url to the current page (not very useful)
        public $pagefiller;
        
        //----------------------------------------
        // construct
        //----------------------------------------
        public function __construct() {
            $this->request = Wi3::$routing->url;
            
            $this->wi3 = "http://" . $_SERVER["HTTP_HOST"] . substr($_SERVER["PHP_SELF"], 0, strpos($_SERVER["PHP_SELF"], "index.php"));
           
            //site and pagefiller location can only be known after site and page have been loaded by Engine or View
            //we therefore register an hook on the wi3.siteandpageloaded event
            Event::add("wi3.siteandpageloaded", array("Wi3_urlof", "fillsiteandpagefiller"));
        }
        
        //----------------------------------------
        // functions that create urls on the fly
        //----------------------------------------
        public function page($page) {
            //fetch url if a ORM object is given
            //otherwise, just return the correct url directly
            return $this->site . (is_object($page) ? $page->url : $page);
        }
        
        public function file($file) {
            return $this->site . "data/files/" . (is_object($file) ? $file->url : $file);
        }
        
        public function image($file, $xsize=-1) {
            return $this->site . "data/files/" . ($xsize != -1 ? $xsize . "/" : "") . basename((is_object($file) ? $file->url : $file));
        }
        
        //----------------------------------------
        // functions that fills ->site, ->page and ->pagefiller urls after the wi3.siteandpageloaded Event
        //----------------------------------------
        public static function fillsiteandpagefiller() {
            //determine the url of the site
            //there are 2 options: 
            // - this page is loaded directly in Wi3, with full arguments attached (controller/action/args)
            // - this page is loaded from a redirect, with only the page as segment
            $segpos = (count(Wi3::$routing->segments) > 0? strpos(Wi3::$routing->url, implode("/", Wi3::$routing->segments)) : -1);
            if ($segpos > 0) { 
                //page is loaded from within Wi3 with segments written all out
                Wi3::$urlof->site = Wi3::$urlof->wi3 . "sites/" . Wi3::$site->url . "/";
            } else {
                //only page-name is given as segment, like http://domain.com(/something)/page
                //what site Wi3 should pick, is then embedded in the http://domain(/something), so Wi3::$urlof->site then should return http://domain.com(/something/...) without the /page
                $ractionargs = array_reverse(Wi3::$routing->actionargs);
                $pagepos = (count($ractionargs) > 0 ? strpos(Wi3::$routing->url, $ractionargs[0]) : -1);
                if ($pagepos > 0) {
                    Wi3::$urlof->site = substr(Wi3::$routing->url, 0, $pagepos);
                } else {
                    //page does not even exist in URL, so the URL does not specify a page, so the full url should be used (eg http://domain.com/)
                    Wi3::$urlof->site = Wi3::$routing->url;
                }
            }
            if (isset(Wi3::$page)) { 
                Wi3::$urlof->page = Wi3::$urlof->site . Wi3::$page->url;
                Wi3::$urlof->pagefiller = Wi3::$urlof->wi3 . "pagefillers/" . Wi3::$page->page_filler . "/";
            }
        }
        
        
    }

?>