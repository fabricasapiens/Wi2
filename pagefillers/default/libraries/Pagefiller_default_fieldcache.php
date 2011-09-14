<?php

    class Pagefiller_default_fieldcache extends Wi3_cache {
        
        //----------------------------------------
        // field cache setup
        // is called by the pagefiller's enablefieldcache hook
        // and enables the fieldcache within wi3
        //----------------------------------------
        public function setup() {
            Wi3::$departmentclasses["cache"] = "Pagefiller_default_fieldcache"; //the cache department will be the Fieldcache one when the Wi3 departments are created
        }
      
        //----------------------------------------
        // field and sitefield (where the $field->page is not available) cache functions
        //----------------------------------------
        public function field_get($field, $addendum) {
            return Cache::instance()->get("wi3_field_" . $field->type . "_" . $field->id . "_" . $addendum);
        }
        
        public function field_set($field, $addendum, $data) {
            //create tags, so that we can find (and/or delete) ALL caches of a certain field or certain type of field at once
            if (isset($field->page)) {
                $tags = array("wi3", "wi3_field", "wi3_field_" . $field->type, "wi3_fieldwherepage_" . $field->page->id, "wi3_field_" . $field->type . "_" . $field->id);
            } else {
                $tags = array("wi3", "wi3_field", "wi3_field_" . $field->type, "wi3_field_" . $field->type . "_" . $field->id);
            }
            if (is_object(Wi3::$site)) { $tags[] = "wi3_fieldwheresite_" . Wi3::$site->id; }
            return Cache::instance()->set("wi3_field_" . $field->type . "_" . $field->id . "_" . $addendum, $data, $tags);
        }
        
        public function field_get_all($field) {
            //when we set a cache, we give it a tag for that specific field (without addendum)
            //so we can get them all by that tag
            return Cache::instance()->find("wi3_field_" . $field->type . "_" . $field->id);
        }
        
        //delete a specific field +addendum
        public function field_delete($field, $addendum) {
            if (isset($field->page_id)) {
                //if a field is not to be cached, the page is not to be either
                //remove the page cache
                self::page_delete_all_wherepage($field->page);
            } else {
                //this probably is a sitefield, so unfortunately we need to remove the cache of ALL pages (as this field could appear in any page)
                self::page_delete_all_wheresite($field->site);
            }
            return Cache::instance()->delete("wi3_field_" . $field->type . "_" . $field->id . "_" . $addendum);
        }
        
        //delete all caches from a specific field, regardless the addendum
        public function field_delete_all_wherefield($field) {
            if (isset($field->page_id)) {            
                //if a field is not to be cached, the page is not to be either
                //remove the page cache
                self::page_delete_all_wherepage($field->page);
            } else {
                //this probably is a sitefield, so unfortunately we need to remove the cache of ALL pages (as this field could appear in any page)
                self::page_delete_all_wheresite($field->site);
            }
            //when we set a cache, we give it a tag for that specific field (without addendum)
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_field_" . $field->type . "_" . $field->id);
        }
        
        public function field_delete_all_wherepage($page) {
            //if a field is not to be cached, the page is not to be either
            //remove the page cache
            self::page_delete_all_wherepage($page);
            //when we set a cache, we give it a tag for that specific page
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_fieldwherepage_" . $page->id);
        }
        
        public function field_delete_all_wheresite($site) {
            //if a field is not to be cached, the page is not to be either
            //remove the page cache
            self::page_delete_all_wheresite($site);
            //when we set a cache, we give it a tag for that specific page
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_fieldwheresite_" . $site->id);
        }
        
        public function field_delete_all_wherefieldtype($fieldtype) {
            //if alle fields with a certain fieldtype are not to be cached, 
            //*ALL* the pages that have a field with this fieldtype should delete their caches as well
            //$fields = Database::instance()->select("*")->from("fields")->where("type", $fieldtype)->get();
            $fields = ORM::factory("field")->where("type", $fieldtype)->find_all();
            foreach($fields as $field) {
                if (isset($field->page_id)) { 
                    self::page_delete_all_wherepage($field->page);
                } else {
                    //this probably is a sitefield, so unfortunately we need to remove the cache of ALL pages (as this field could appear in any page)
                    self::page_delete_all_wheresite($field->site);
                }
            }
            //when we set a cache, we give it a tag for that specific fieldtype
            //so we can get them all by that tag
            return Cache::instance()->delete_tag("wi3_field_" . $fieldtype);
        }
        
        public function field_delete_all() {
            //also delete all page caches
            self::page_delete_all();
            return Cache::instance()->delete_tag("wi3_field");
        }
      
    }

?>