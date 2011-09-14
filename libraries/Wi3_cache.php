<?php

    class Wi3_cache {
        
        //----------------------------------------
        // page cache functions
        // if a pagefiller wants to use additional cache functions, it should add a hook into the wi3.aftersetup event
        // and make Wi3::$cache into a class that extend Wi3_cache and adds the extra functions
        //----------------------------------------
        public static $page_cacheable = false;             
        public static $page_addendums = array();      //page-addendums that any component might set. They can be used by a pagefiller to decide if and how they cache their data
                                                                                    //the default pagefiller sorts this addendums array and implodes it to a string. Then it is used as a 'page-addendum'
                                                                                    //The Login_Controller and LoginIfPossible_Controller will set the username of a logged-in user as 'wi3_login_username' => $username
                                                                                    //So that every user will have its own cached page. For example the menu of a site will then be cached user-specific
        
        public function page_get($page, $addendum) {
            return Cache::instance()->get("wi3_page_" . $page->id . "_" . $addendum);
        }
        
        public function page_set($page, $addendum, $data) {
            //create tags, so that we can find (and/or delete) ALL caches of a certain field or certain type of field at once
            $tags = array("wi3", "wi3_page", "wi3_page_" . $page->id);
            if (is_object(Wi3::$site)) { 
                $tags[] = "wi3_pagewheresite_" . Wi3::$site->id;
            }
            return Cache::instance()->set("wi3_page_" . $page->id . "_" . $addendum, $data, $tags);
        }
        
        public function page_delete($page, $addendum) {
            return Cache::instance()->delete("wi3_page_" . $page->id . "_" . $addendum);
        }
        
        public function page_delete_all_wherepage($page) {
            //when we set a cache, we give it a tag for that specific page (without addendum)
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_page_" . $page->id);
        }
        
        public function page_delete_all_wheresite($site) {
            //when we set a cache, we give it a tag for that specific site (without addendum)
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_pagewheresite_" . $site->id);
        }
        
        public function page_delete_all() {
            //when we set a cache, we give it a tag for that specific page (without addendum)
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_page");
        }
    }

?>