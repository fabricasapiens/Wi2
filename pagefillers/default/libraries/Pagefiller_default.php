<?php

    //A pagefiller SHALL have three functions
    // - edit_page($page) which renders the content of a page (to be pasted into the used template) in edit mode
    // - view_page($page) which renders the content of the very same page, but in just view mode
    // - page_properties($page, $key) which returns extra page properties, like $page->fields when they are not included in the standard page model
    // - event($eventname, $data) which responds to events like "page_added" or "page_removed"
    //
    // The edit_page can assume a logged-in Wi3 user (Wi3::$user), as of the view_page can NOT !
    
    class Pagefiller_default {
        
        public static $field; //put all the field specific functions in $this->field to prevent too much code in one place...
        
        public function __construct() {
            //put all the field specific functions in $this->field to prevent too much code in one place...
            if (!is_object(self::$field)) {
                self::$field = new Pagefiller_default_field();
            }
        }
        
        //------------------------------------------------------------------
        // Pagefiller STANDARD OBLIGATORY functions and variables
        // - page_properties() : function that returns page properties of a page with this pagefiller (ie $page->fields, which does not exist in the standard page model)
        // - event() : dealing with certain events (i.e. a page added with this pagefiller, or the upgrade of a page with this pagefiller)
        // - edit_page() : called to render a page, when the user is in the wi3 workplace
        // - view_page() : called to render a page when the user outside the wi3 workplace
        //------------------------------------------------------------------
        
        public $edit_page_inserts;
        
        //This page filler is basically a field renderer
        //It grabs all the fields of this page and renders them into the page->page_template
        
        //returns extra page properties, like $page->fields when they are not included in the standard page model
        function page_properties($page, $key) {
            
            if ($key == "fields") {
                return ORM::factory("field")->where("page_id", $page->id)->find_all();
            }
            
            return false;
            
        }
        
        //responds to certain events that are fired from ajaxengine (when a page is added/moved/deleted) and others (i.e. an upgrade is executed)
        //the $data is the page that is currently added/moved/deleted
        public function event($eventname, $data) {
            if ($eventname == "page_added") {
                //the page we want to handle, is in the $data parameter
                $page = $data;
                
                //get the pagetypes from the config
                //and specifically the pagetype of the created page
                $pfpage = $this->prefilled_page($page->page_type);
                
                if (!isset($pfpage["pagefillerspecific"]) OR !isset($pfpage["pagefillerspecific"]["dropzones"])) {
                    return; //there are no dropzones to be filled with fields
                }
                
                foreach($pfpage["pagefillerspecific"]["dropzones"] as $dropzone_id => $dropzone) {
                    //check if there are default fields that we should add to this dropzone on this page right now
                    if (isset($dropzone["defaultFields"])) {
                        //yes, we need to add some fields to this dropzone as they are default
                        $fieldtypes = $dropzone["defaultFields"];
                        $fieldtypes = array_reverse($fieldtypes);    //reverse array because we stack fields from bottom to top, while the "defaultFields" are described from top to bottom
                        foreach($fieldtypes as $index => $fieldtype) {
                            //fieldtype might be an array, describing the field in question
                            $config = Array();
                            if (is_array($fieldtype)) {
                                $config = (isset($fieldtype["config"]) ? $fieldtype["config"] : Array());
                                $fieldtype = (isset($fieldtype["fieldtype"]) ? $fieldtype["fieldtype"] : Array());
                            }
                            ob_start();
                            self::$field->addFieldToDropZone($page->id, $dropzone_id, $fieldtype, $config); //cache is deleted here!
                            ob_end_clean();
                        }
                    } else {
                        continue; //go to next dropzone if there are no default fields found
                    }
                }
            } else if ($eventname == "upgrade") {
                //$data is an array : ["page"] => $page, ["from"] => version_old, ["to"] => version_new
                $page = $data["page"];
                $from = $data["from"];
                $to = $data["to"];
                if ($from*1 == 1 AND $to*1 == 2) {
                    //check all fields of this page and fire the upgrade event to the components of these fields
                    //these components should then upgrade the data they handle
                    $falseroot = ORM::factory("field");
                    $falseroot->leftnr = 0;
                    $falseroot->rightnr = '9999999999999999';
                    $falseroot->scope = $page->id;
                    $imaginaryroot = $falseroot->get_tree($falseroot);
                    foreach($imaginaryroot->children as $dropzone) {
                        foreach($dropzone->children as $child) {
                            echo "component type aanpassen...<br />";
                            echo "type: " . $child->type . "<br />";
                            //change the componentname from text to component_text
                            if (strtolower($child->type) == "text") { 
                                $child->type = "component_text"; 
                                $child->save();                                
                            }
                            if (strtolower($child->type) == "afbeelding") { 
                                $child->type = "component_niceimage"; 
                                $child->save();                                
                            }
                            if (strtolower($child->type) == "contactform") { 
                                $child->type = "component_univelektra_contactform"; 
                                $child->save();                                
                            }
                            //do the upgrade
                            $component = self::factory("component", $child->type);
                            if (is_object($component)) {
                                $componentarray = $data;
                                $componentarray["field"] = $child;
                                if (method_exists($component, "event")) {
                                    $component->event("upgrade", $componentarray);
                                }
                            }
                        }
                    }
                }
            }
        }
        
        //renders a certain page, but with edit-controls included
        //@ return : a View object
        function edit_page($page) {
            //now return the page as it was produced without editing
           $pagecontent = $this->view_page(Wi3::$site,$page);
            //return pagecontent
            return $pagecontent;
        }
        
        //renders a page, if necessary with edit-controls
        //@ return : a View object
        // ! this function can NOT assume a logged-in Wi3 user !
        function view_page($site, $page) {
            
            if (!Wi3::$site) { Wi3::$site = $site; }
            
            //if we are in edit-mode, we want to include the javascript that enables editing
            if (Wi3::$editmode == true) {
                Wi3::$plugins->load("plugin_jquery_1_4_2_core");
                Wi3::$plugins->load("plugin_jquery_1_4_2_fancybox");
                Wi3::$pagefiller->javascript("pagefiller_default_edit_site.js");
            } else {
                //we are not in edit-mode 
                //this can be because:
                //1. the current user is in wi3 workplace, but is not allowed to edit the page
                //2. the user requests the page from outside wi3 workplace
                if (Wi3::$routing->controller == "engine") {
                    //if we are not in edit-mode, but are still called from the engine controller, 
                    //then the user has not the right to edit the page, but still is in the workplace
                    //thus, just make the links point to edit-pages, but do not enable editing on this particular page
                    Wi3::$plugins->load("plugin_jquery_1_4_2_core");
                    Wi3::$plugins->load("plugin_jquery_1_4_2_fancybox");
                    Wi3::$pagefiller->javascript("pagefiller_default_edit_links.js");
                }
            }
            
            //to do the caching check, we need to know what fields are within this page
            $falseroot = ORM::factory("field");
            $falseroot->leftnr = 0;
            $falseroot->rightnr = '9999999999999999';
            $falseroot->scope = $page->id;
            $imaginaryroot = $falseroot->get_tree($falseroot);
                        
            //the template has a few 'fieldzones' / 'dropzones' where the fields of this page will be dropped
            //these zones can be some header with one field, or a column with a lot of fields
            //we will now fill these zones from the fieldstructure
            
            //imaginaryroot -> fieldzone1 (with certain title) -> field 1
            //                                                                        -> field 2
            //                       -> fieldzone2 (with certain title) -> field 1
            
            // PAGE HOOKS
            // sites can include hooks that can influence certain Wi3 settings, or that can hook into certain Events
            $hookpath = Wi3::$pathof->site . "hooks/";
            if (is_readable($hookpath))
			{
				$hooks = (array) glob($hookpath.'*');

				if ( ! empty($hooks))
				{
					foreach ($hooks as $index => $hook)
					{
						$hook = str_replace('\\', '/', $hook);
                        include($hook);
                    }
                }
            }
            
            // CACHING CHECK
            // initial caching-values are fetched from the Wi3::$template instance
            // then, all field-components are loaded, which can override these values
            // in the meanwhile, the cache-addendums are set. These addendums are 'names' that enable distinghuish different caches on 1 page (i.e. different caches for different logged-in users) 
            // after this whole check, it will be determined whether the current page should be loaded from cache, and if not, if it should (after rendering) be saved to cache
            // the loading and saving of cache happens according to the aforementioned cache-addendum
            
            // initial cache and addendum values 
            // the ::$template values both default to false, but can be set to true in a hook (see /hooks in a site-folder and above, where these hooks are run)
            $cacheonpage = array("loadfromcache" => Wi3::$template->loadfromcache, "cache" => Wi3::$template->cacheable, "addendum" => "");
            Wi3::$cache->page_addendums["pagefiller_default_editmode"] = (Wi3::$editmode == true ? "edit" : "normal"); //create 'edit' addendum if we are in edit-mode, because these pages almost always differ from the 'view' versions   
                // Anything can set an addendum in Wi3::$cache->page_addendums. The Login_Controller and LoginIfPossible_Controller set the user that is logged in.
                // Thus, Wi3::$cache->page_addendum["wi3_login_userid"] will now contain the id of the currently logged in user (= Wi3::$user->id)
                // We have just set the editmode, and from here-on, any component can set anything extra in the page addendum
                //30 lines down, we will implode the addendum-array (including the "pagefiller_default_editmode" (that sets the edit-mode) one)  to a string and use it as the page-addendum
                //
                // KEEP IN MIND: components do NOT have to differentiate their addendum between different users and between viewmode and edit-mode. 
                // THE LOGIN CONTROLLER AND THE ABOVE PAGE_ADDENDUM ALREADY TAKE CARE OF THAT!
            
            // now load all the field-components and let them set their cacheability and cache-addendums
            $componentinstances = array();
            $cacheondropzones = array();
            $cacheonfields = array();
            if (!empty($imaginaryroot->children)) { //if there are dropzones
                foreach($imaginaryroot->children as $dropzone) {
                    //get name of dropzone (we use the 'type' column for that)
                    $dropzonename = $dropzone->type;
                    //set caching on this dropzone on true
                    //it will be set on false if a subfield cannot be cached
                    $cacheondropzones[$dropzonename] = array("cache" => true, "addendum" => "");
                    //every dropzone can contain fields (as children)
                    if ($dropzone->children) {
                        $this->template->$dropzonename = "";
                        foreach($dropzone->children as $field) {
                            if (empty($field->type)) {
                                $field->type = "text";
                            }
                            $component= self::factory("component", $field->type);
                            $componentinstances[$field->id] = $component;
                            //check whether this particular component is cachable
                            //components can set whether they are cachable in $component->wi3_cachable = true;
                            //components can make that a 'static' property, or they can set it in the __construct() to enable caching only sometimes
                            if (isset($component->wi3_cacheable) AND $component->wi3_cacheable == true) {
                                $cacheonfields[$field->id]["cache"] = true;
                                $cacheonfields[$field->id]["addendum"] = (isset($component->wi3_cachetitle) ? $component->wi3_cachetitle : "");
                                //we add the field-addendum to the page-addendum
                                //if there are different combinations of addendums, there will be a different cache for each combination
                                //so ie a pagecache for in edit-modus, or a pagecache for a page with a field with different states (ie showing a certain image at random, and making the title of that image the addendum)
                                $cacheonpage["addendum"] .= $cacheonfields[$field->id]["addendum"];
                            } else {
                                $cacheonfields[$field->id]["cache"] = false;
                                //also, this dropzone cannot be cached
                                $cacheondropzones[$dropzonename]["cache"] = false;
                            }
                        }
                    }
                    if ($cacheondropzones[$dropzonename]["cache"] == false) {
                        //if a dropzone cannot be cached, a page cannot be cached as well
                        $cacheonpage["cache"] = false;
                    }
                }
            }
            //load the page-addendums, and prepend these to the component-addendums that might have been set.
            asort(Wi3::$cache->page_addendums); //sort alphabetically, so that the order of cache-addendums does not matter, but only the different factual addendums in their content
            $page_addendums = implode("_", Wi3::$cache->page_addendums);
            $cacheonpage["addendum"] = $page_addendums . $cacheonpage["addendum"];   //prepend it to the existing addendum
            
            // LOAD FROM CACHE
            // if the page should be loaded from cache, it will
            //now that we know what we can get from cache, do that, starting with the biggest possible cache, the page-cache
            if ($cacheonpage["loadfromcache"] == true) {
                //Wi3::cache_page_delete($page, $cacheonpage["addendum"]);
                $pagecache = Wi3::$cache->page_get($page, $cacheonpage["addendum"]);
                if ($pagecache == null) { 
                    //there is no page cache yet, it will be set after the page has been rendered
                } else {
                    //if we have a full page cache, we do NOT want any extra css or javascript
                    //this is already contained in the cache
                    //clear all events that could alter the page in the system.display Event phase
                    Event::clear("system.display");
                    //echo "pagecache!";
                    return View::factory("wi3/empty")->set("content", $pagecache);
                }
            }
            
            // NO LOADING FROM CACHE
            // if we get here, the page was not loaded from cache
            // thus, the page should be rendered on itself
            // after that, the result might be saved to cache, depending on the ->cacheable option
            
            //load the page_template
            //if no template is set, set it and save the page
            if (empty($page->page_template)) { $page->page_template = "default"; $page->save(); }
            //load the page-template from either the users/ folder or the wi3/ folder
            //based on the preference
            //if there is no template type set, assume the wi3 folder and save page
            if (empty($page->page_templatetype)) { $page->page_templatetype = "wi3"; $page->save(); }
            $view = new ExternalView(Wi3::$pathof->pagetemplate . basename($page->page_template) . ".php");
            $this->template = $view;
            
            //before adding full page structure and all fields of the page, we need to fetch those first
            //get all the pages of this site for displaying them as a navigation-tree and the like
            $falseroot_p = ORM::factory("page");
            $falseroot_p->leftnr = 0;
            $falseroot_p->rightnr = '9999999999999999';
            $falseroot_p->scope = $site->id;
            $imaginaryroot_p = $falseroot_p->get_tree($falseroot_p);
            
            //give some extra information to the template, so that the Template can use that
            $this->template->site = $site;
            $this->template->page = $page;
            $this->template->pages = $imaginaryroot_p->children;
            $this->template->fields = $imaginaryroot->children;
            
            //if we wanted pagecache, but didn't get it, we try dropzone-cache
            //if ($pagecached == false) {
                //get cache from dropzones
            //}
            
            //PRODUCE PAGE, IF STILL NECCESARY
            if (!empty($imaginaryroot->children)) { //if there are dropzones
                foreach($imaginaryroot->children as $dropzone) {
                    //get name of dropzone (we use the 'type' column for that)
                    $dropzonename = $dropzone->type;
                    //every dropzone can contain fields (as children)
                    if ($dropzone->children) {
                        $this->template->$dropzonename = "";
                        foreach($dropzone->children as $field) {
                            //get component from the already created componentinstances
                            $component = $componentinstances[$field->id];
                            
                            //render content in the field_edit
                            $this->template->$dropzonename .= 
                            "<div id='wi3_field_" . $field->id . "'  class='";
                            //check whether we should prevent encapsulating the field with the "class='wi3_field'" (which would cause yellow borders and editability)
                            //prevention is done by setting $component->prevent_wi3_field_class = true;
                            if (!isset($component->prevent_wi3_field_class) OR $component->prevent_wi3_field_class != true) {
                                $this->template->$dropzonename .= "wi3_field ";
                            }
                            //add the component class to this field
                            $this->template->$dropzonename .= $field->type . "'";
                            if ($cacheonfields[$field->id]["cache"] == true) {
                                
                                //a field can have different caches, based on certain circumstances (ie, when it shows different content (ie, random images))
                                //a field can set the 'wi3_cachetitle' for that, like $component->wi3_cachetitle
                                //the page also sets some addendums, like the edit-mode and which user is currently logged in
                                $addendum = $cacheonpage["addendum"] . $cacheonfields[$field->id]["addendum"];
                                
                                //fetch from cache, if it exists
                                $content = Wi3::$cache->field_get($field, $addendum);
                                if ($content== null) {
                                    //cache does not exist yet
                                    //so create it
                                    ob_start();
                                    $content = $component->render_field($field);
                                    $content = ob_get_contents() . $content; //paste the echo-ed content in front
                                    ob_end_clean();
                                    //set cache, only if the component wants that
                                    if (isset($component->wi3_cacheable) AND $component->wi3_cacheable == true) {
                                        Wi3::$cache->field_set($field, $addendum, $content);
                                    }
                                }
                                
                            } else {
                                ob_start();
                                $content = $component->render_field($field);
                                $content = ob_get_contents() . $content; //paste the echo-ed content in front
                                ob_end_clean();
                            }
                            
                            //unset component instance of this field
                            unset($componentinstances[$field->id]);
                            unset($component);
                            
                            $this->template->$dropzonename .= ">" .
                            $content .
                            "</div>";
                            //render to the current dropzone in the page_template ($this->template->content)
                        }
                    }
                }
            }
            
            //SAVE PAGE TO CACHE, IF POSSIBLE
            //if we get here, there was no pagecache available, nor all the dropzonecaches
            //if $cacheonpage was set to true, we will now cache the rendered page and continue
            if ($cacheonpage["cache"] == true) {
                //set an event so that the complete page (with css, javascript etc added) will be cached just before the page is flushed
                Event::add('system.display', array('Pagefiller_default','cache_cache_output'));
                self::$cache_page = $page;
                self::$cache_addendum = $cacheonpage["addendum"];
            }
            
            if (isset($edit_page_inserts)) {
                //deze page_inserts invoegen na de <head> tag in de template
                $html = $this->template->render();
                $html = str_replace("<head>", "<head>" . $edit_page_inserts, $html);
                $this->template = View::factory("wi3/empty")->set("content", $html);
            }
            
            return $this->template;
        }
        
        //------------------------------------------------------------------
        // Pagefiller SPECIFIC, EXTRA functions and variables
        //------------------------------------------------------------------
        
        static function prefilled_pages() {
            //get alle the prefilled pages, ordered by dropzone-id
            $pagetypesfile = Wi3::$config->site("pagetypes");
            return $pagetypesfile["pagetypes"];
        }
        
        static function prefilled_page($type = "default") {
            $prefilled_pages = self::prefilled_pages();
            if (isset($prefilled_pages[$type])) {
                return $prefilled_pages[$type];
            } else if (isset($prefilled_pages["default"])) {
                return $prefilled_pages["default"];
            } else {
                return false;
            }
        }
        
        //------------------------------------------------------------------
        // Factory creation of components, site_fields and views
        //------------------------------------------------------------------
        static function factory($type, $id, $args = array()) {
            if (!empty($type) AND !empty($id)) {
                if ($type == "component") {
                    //create component instance
                    //components always reside in the /wi3/components/$componentname folder
                    if (file_exists(APPPATH . "pagefillers/default/components/" . strtolower($id) . "/libraries/" . ucfirst($id) . ".php")) {
                        include_once(APPPATH . "pagefillers/default/components/" . strtolower($id) . "/libraries/" . ucfirst($id) . ".php");
                        return new $id();
                    } else {
                        return "component does not exist at " . APPPATH . "pagefillers/default/components/" . strtolower($id) . "/libraries/" . ucfirst($id) . ".php";
                    }
                    return null;
                } else if ($type == "siteField") {
                    $field = ORM::factory("siteField")->where("site_id", Wi3::$site->id)->where("title", $id)->find();
                    if ($field->id > 0) {
                        //the field exists
                        //however, check given arguments, like the type of this field
                        //to see if the existing field differs from the field the page wants to see
                        $changed = false;
                        foreach($args as $key => $value) {
                            if ($field->$key != $value) {
                                //then there is something wrong, or at least it is not the field that it was previously
                                //change it (back) to what the page 'wants' to see
                                $field->$key = $value;
                                $changed = true;
                            }
                        }
                        if ($changed) { $field->save(); }
                        return $field;
                    } else {
                        //try to create the field
                        $field->title = $id;
                        //check given arguments, like the type of this field
                        foreach($args as $key => $value) {
                            $field->$key = $value;
                        }
                        $field->site_id = Wi3::$site->id;
                        $field->save();
                        return $field;
                    }
                } else if ($type == "view") {
                    $sitefolder = self::get_site_folder();
                    //first try to find the view in the user folder
                    if (file_exists($sitefolder . "views/" . $id. ".php")) {
                        return new ExternalView($sitefolder . "views/" . $id. ".php");
                    } else {
                        //else, just a load a common view, pulled from the usual Wi3/views folder
                        return View::factory($id);
                    }
                } else {
                    return false;
                }
            }
        }
        
        public static $cache_page;
        public static $cache_addendum = "";
        public function cache_cache_output() {
            //while execution takes place, it can happen that the page does not want to be cached
            //check for that and only continue if caching is all right
            if (Wi3::$template->cacheable == true) { 
                Wi3::$cache->page_set(self::$cache_page, self::$cache_addendum , Event::$data);
            }
        }
        
    }




?>
