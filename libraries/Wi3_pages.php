<?php

    class Wi3_pages {
        
        public function add($type = "", $under = "") {
            //get current site
            $site = Wi3::$site;
            //to add a page, one needs to be admin for this site
            if (!Wi3::$rights->check("admin", $site)) {
                return false; //user is not allowed to add a page
            } else {
                //create new page with the given type
                $page = ORM::factory("page");
                $page->site_id = $site->id; //page belongs to current site
                $page->title = "nieuwe pagina"; //site-id should be known in order to auto-generate a valid URL from this title. Thus, set the site_id before this line
                $page->page_filler = "default";
                $page->page_type = $type;
                $page->page_templatetype = $site->default_page_templatetype; //inherit the default page templatetype
                $page->page_template = $site->default_page_template; //inherit the default page template
                //rights or 'roles' that are required for a certain action
                $page->user_id = Wi3::$user->id;
                $page->moveright = "siteadmin"; //even without setting this, the siteadmin can always move all pages around
                $page->viewright = ""; //everyone can view
                $page->editright = "_" . Wi3::$user->username;  //every user has a role equal to "user_"+ID. All users can edit their own page.
                $page->adminright = "_" . Wi3::$user->username; //for editing one of the page-rights or for deleting the page
                
                $page->scope = $page->site_id;
                //insert as last node (initially, maybe move it later: see below)
                $lastnode = $page->get_last_root();
                if (is_object($lastnode)) {
                    $page->insert_as_next_sibling_of($lastnode); //become last node
                } else {
                    //become root, as there are no pages yet
                    $page->make_root();
                }
                //check if we need to move this page under another page
                if (preg_match("@treeItem_[0-9]+@i", $under)) {
                    $refpage = ORM::factory("page", substr($under,9));
                    $page->move_to_first_child_of($refpage);
                }
                
                //notify the pagefiller that a page has been added
                $pagefillername = "Pagefiller_" . $page->page_filler;
                $pagefiller = new $pagefillername();
                $pagefiller->event("page_added", $page);
                
                return $page;
            }
        }
        
        public function moveBefore($page, $refpage) {
            //create ORM objects if just IDs are given
            if (is_numeric($page)) {
                $page = ORM::factory("page", $page);
            }
            if (is_numeric($refpage)) {
                $refpage = ORM::factory("page", $refpage);
            }
            //check rights on the pages
            if (!Wi3::$rights->check("move", $page) OR !Wi3::$rights->check("move", $refpage)) {
                return false;
            }
            
            if ($refpage AND $page) {
                $page->move_to_prev_sibling_of($refpage);
                $page->reload();
                return true;
            }
        }
        
        public function moveAfter($page, $refpage) {
            //create ORM objects if just IDs are given
            if (is_numeric($page)) {
                $page = ORM::factory("page", $page);
            }
            if (is_numeric($refpage)) {
                $refpage = ORM::factory("page", $refpage);
            }
            //check rights on the pages
            if (!Wi3::$rights->check("move", $page) OR !Wi3::$rights->check("move", $refpage)) {
                return false;
            }
            
            if ($refpage AND $page) {
                $page->move_to_next_sibling_of($refpage);
                $page->reload();
                return true;
            }
        }
        
        public function moveUnder($page, $refpage) {
            //create ORM objects if just IDs are given
            if (is_numeric($page)) {
                $page = ORM::factory("page", $page);
            }
            if (is_numeric($refpage)) {
                $refpage = ORM::factory("page", $refpage);
            }
            //check rights on the pages
            if (!Wi3::$rights->check("move", $page) OR !Wi3::$rights->check("move", $refpage)) {
                return false;
            }
            
            if ($refpage AND $page) {
                $page->move_to_last_child_of($refpage);
                $page->reload();
                return true;
            }
        }
        
        public function delete($page) {
            //create ORM objects if just IDs are given
            if (is_numeric($page)) {
                $page = ORM::factory("page", $page);
            }
            //check rights on the pages
            if (!Wi3::$rights->check("delete", $page)) {
                return false;
            }
            
            if ($page) {
                $page->delete();
                return true;
            }
        }
        
        //-------------------------------------------------------------
        // function to retrieve certain pages
        //-------------------------------------------------------------
        public function get_default_page() {
            $site = Wi3::$site;
            $page = $site->pages[0];
            if (is_object($page)) {
                $page = $page->get_root($page);
                //if the site is multilanguage, we want to have NOT the root page, but the first CHILD of the root page
                if (isset($modules["site_multilanguage"]) AND $page->has_descendants()) {
                    $children = $page->get_children(true);
                    $page = $children[0];
                }
            }
            return $page;
        }
        
        public function get_page($pageid) {
            if (is_object($pageid)) {
                //if the page is by change a page, just return the page with that ID (there might be changes to the $pageid page, we just want to return the page as in the DB)
                return ORM::factory("page", $pageid->id);
            } else {
                $site = Wi3::$site;
                $modules = $site->modules; //always returns an Array
                
                if (empty($pageid)) {
                    //if there's no page, then get the default page
                    return self::get_default_page();
                } else {
                    //if name is set, then load the correct page
                    if (is_numeric($pageid)) {
                        $page = ORM::factory("page")->where("id", $pageid)->where("site_id", $site->id)->find();
                    } else {
                        $page = ORM::factory("page")->where("url", $pageid)->where("site_id", $site->id)->find();
                    }
                   
                    //if failed, get the first root page anyway
                    if (!is_object($page) OR $page->id == 0) {
                        return self::get_default_page();
                    }
                    
                    return $page;
                }    
            }
        }
        
    }

?>