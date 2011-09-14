<?php

    //-------------------------------------------------------------
    // This controller contains all the functions that are executed from within Wi3 via AJAX
    //-------------------------------------------------------------

    //you need to be logged in to view any of this controller's pages,
    //so extend with Login_Controller
    class Ajaxengine_Controller extends Login_Controller {
      
        public $template = "wi3/ajax";
        
        public function __construct() {
            //run the Login controller constructor first so that login is executed and Wi3::$site and Wi3::$page are correctly loaded
            parent::__construct(); 
            //now run the event that page and site are loaded
            //Wi3_pathof and Wi3_urlof hook into this to fetch the path of the current site and of the pagefiller
            Event::run("wi3.siteandpageloaded");
        }
        
        //-------------------------------------------------------------
        // Control panel
        //-------------------------------------------------------------
        //SiteTitle
        public function changeSiteTitle($title) {
            
            if (Wi3::$rights->check("admin", Wi3::$site) == true) {
            
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                
                Wi3::$site->title = $title;
                Wi3::$site->save();
                echo json_encode(
                    Array(
                        "alert" => "Sitetitel aangepast naar '" . $title . "'"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Sitetitel NIET aangepast"
                    )
                );
            }
        }
        
        //Template
        public function useUserTemplate($name) {
            $site = Wi3::$site;
            if (Wi3::$rights->check("admin", $site)) {
                $usertemplatedir = Wi3::$pathof->site . "page_templates/";
                $files = glob($usertemplatedir . "*.php");
                //check whether the specified template is valid
                if (in_array($usertemplatedir . $name . ".php", $files)) {
                    
                    //#remove cache of the *complete* site!
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    //use the template in all pages of this site
                    foreach($site->pages as $page) {
                        $page->page_templatetype = "user";
                        $page->page_template = $name;
                        $page->save();
                    }
                    //also set this template as the default for new pages
                    $site->default_page_templatetype = "user";
                    $site->default_page_template = $name;
                    $site->save();
                    echo json_encode(
                        Array(
                            "alert" => "Template voor alle pagina's gewijzigd naar '" . $name . "'",
                            "scriptsafter" => Array("$('#template_picker a').removeClass('bold').filter('#template_user_" . $name . "').addClass('bold');")
                        )
                    );
                }    
            } else {
                echo json_encode(
                        Array(
                            "alert" => "Template voor alle pagina's NIET gewijzigd"
                        )
                    );
            }
        }
        
        //Wi3 templates
        public function useWi3Template($name) {
            if (Wi3::$rights->check("admin", Wi3::$site)) {
                $wi3templatedir = Wi3::$pathof->site . "page_templates/";
                $files = glob($wi3templatedir . "*.php");
                //check whether the specified template is valid
                if (in_array($wi3templatedir . $name . ".php", $files)) {
                    
                    //use the template in all pages of this site
                    $site = Wi3::$user->sites[0];
                    foreach($site->pages as $page) {
                        $page->page_templatetype = "wi3";
                        $page->page_template = $name;
                        $page->save();
                    }
                    
                    //#remove cache of the *complete* site!
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    //also set this template as the default for new pages
                    $site->default_page_templatetype = "wi3";
                    $site->default_page_template = $name;
                    $site->save();
                    echo json_encode(
                        Array(
                            "alert" => "Template voor alle pagina's gewijzigd naar '" . $name . "'",
                            "scriptsafter" =>  Array("$('#template_picker a').removeClass('bold').filter('#template_wi3_" . $name . "').addClass('bold');")
                        )
                    );
                }    
            } else {
                echo json_encode(
                        Array(
                            "alert" => "Template voor alle pagina's NIET  gewijzigd"
                        )
                    );
            }
        }
        
        //-------------------------------------------------------------
        // menu
        //-------------------------------------------------------------
        public function addPage($type = "", $under="undefined") {
            //add a new page. Rights to do this will be checked and the pagefiller will be notified of the "page_added" event as well
            $page = Wi3::$pages->add($type, $under);
            
            if ($page == false) {
                echo json_encode(
                    Array(
                        "alert" => "pagina kon NIET toegevoegd worden",
                    )
                );
            } else {
            
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                
                $li = html::anchor("engine/content/" . $page->id, $page->title);
                echo json_encode(
                    Array(
                        "alert" => "pagina is toegevoegd ",
                        //"dom" => Array("append" => Array("#menu_pages" => "<li class='treeItem' id='treeItem_" . $page->id . "'><span>" . html::anchor("engine/content/" . $page->id, $page->title) . "</span></li>")),
                        "scriptsafter" => Array(
                            "workplace.currentTree().addNode('treeItem_" . $page->id . "','" . addslashes($li) . "')",
                            "editname"  => "wi3_edit_page_settings('#treeItem_" . $page->id . " > span > a');"
                        )
                    )
                );
            }
            
        }
        
        public function movePageBefore($movedpage, $referencepage) {
            $pageid = substr($movedpage,9);
            $refid = substr($referencepage,9);
            if (Wi3::$pages->moveBefore($pageid, $refid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "pagina is verhuisd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "pagina kon NIET verhuisd worden"
                    )
                );
            }
        }
        
       public function movePageAfter($movedpage, $referencepage) {
            $pageid = substr($movedpage,9);
            $refid = substr($referencepage,9);
            if (Wi3::$pages->moveAfter($pageid, $refid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "pagina is verhuisd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "pagina kon NIET verhuisd worden"
                    )
                );
            }
        }
        
        public function movePageUnder($movedpage, $referencepage) {
            $pageid = substr($movedpage,9);
            $refid = substr($referencepage,9);
            if (Wi3::$pages->moveUnder($pageid, $refid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "pagina is verhuisd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "pagina kon NIET verhuisd worden"
                    )
                );
            }
        }
        
        public function deletePage($pagename) {
            $pageid = substr($pagename,9);
            if (Wi3::$pages->delete($pageid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "pagina is verwijderd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "pagina kon NIET verwijderd worden"
                    )
                );
            }
        }
        
        public function startEditPageSettings($pagename) {
            $pageid = substr($pagename,9);
            $editview = View::factory("wi3/ajax/menu_page_settings");
            $editview->site = Wi3::$site;
            $editview->pageid = $pageid;
            if (is_numeric($pageid) AND !empty($pageid)) {
                $page = ORM::factory("page", $pageid);
                if (!empty($page->id)) {
                    //start to edit field
                    $title = $page->title;
                    echo json_encode(
                        Array(
                            "scriptsbefore" => Array("$('#menu_pagesettings_tabs').hide();"),
                            "dom" => Array(
                                "fill" => Array("#menu_pagesettings_tabs" => $editview->render() )
                            ),
                            "scriptsafter" => Array("workplace.menu_pagesettings_enable();", "$('#menu_pagesettings_tabs').show();", "$('#pagetitle').focus()"),
                        )
                    );
                }
            }
        }
        
        public function editPageSettings($pageid) {
            if (is_numeric($pageid) AND !empty($_POST)) {
                $page = ORM::factory("page", $pageid);
                if (Wi3::$rights->check("edit", $page) == true) {
                    $oldname = $page->title;
                    //$langarray = unserialize($page->get("wi3_language_array"));
                    //if (!is_array($langarray)) { $langarray = Array(); }
                    $langarray = Array(); //with saving, always reset the lang-array
                    //get lang of this page
                    $currlang = $page->get_path();
                    $currlang = $currlang[0];
                    
                    $site = Wi3::$site;
                    $modules = $site->modules;
                    if (is_array($modules) AND isset($modules["page_choose_visibility"])) {
                        //set visibility to false. It's a checkbox, so if it is in the $_POST, we set visibility to true again
                        //otherwise, it was not set on the client, so not send, so it will remain false
                        $page->visible = false;
                    }
                    
                    foreach($_POST as $name => $post) {
                        if ($name == "pagetitle") { 
                            $page->title = $post;
                        } else if ($name == "visible") {
                            $page->visible = ($post == "0" ? "0" : "1");
                        } else if (strpos($name,"lang_") === 0 AND !empty($post)) {
                            $langarray[substr($name,5)] = $post; //set related page-id
                            //now, fetch the related page and set related pages for that page as well...
                            $relatedpage = ORM::factory("page", $post);
                            $relatedlangarray = unserialize($relatedpage->get("wi3_language_array"));
                            if (!is_array($relatedlangarray)) { $relatedlangarray = Array(); }
                            foreach($_POST as $name2 => $post2) {
                                if (strpos($name2,"lang_") === 0 AND !empty($post2) AND $post2 != $relatedpage->id ) {
                                    $relatedlangarray[substr($name2,5)] = $post2; //set related page-id
                                }
                            }
                            //add link to this page as well...
                            //add this page to the related page in this page's language
                            $relatedlangarray[$currlang->id] = $page->id;
                            //save
                            $relatedpage->set("wi3_language_array", serialize($relatedlangarray));
                            $relatedpage->save();
                        } else if ($name == "viewright" OR $name == "editright" OR $name == "adminright") {
                            //check for admin privileges
                            if (Wi3::$rights->check("admin", $page)) {
                                $page->$name = $post;
                            }
                        }
                    }
                    
                    //#remove cache of the *complete* site!
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    $langarray[$currlang->id] = $page->id;
                    $page->set("wi3_language_array", serialize($langarray));
                    $page->save();
                    echo json_encode(
                        Array(
                            "alert" => "Pagina-eigenschappen van '" . $oldname . "' succesvol gewijzigd!.",
                            "dom" => Array(
                                "fill" => Array("#treeItem_" . $pageid   . " > span > a" => $page->title)
                            ),
                            "scriptsbefore" => Array("workplace.menu_editdiv_hide()")
                        )
                    );
                }
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                    )
                );
            }
        }
        
        public function editPageTemplateSettings($pageid) {
            if (is_numeric($pageid) AND !empty($_POST)) {
                $page = ORM::factory("page", $pageid);
                if (Wi3::$rights->check("edit", $page) == true) {
                    
                    $page->page_templatetype = $_POST["templatetype"];
                    $page->page_template = $_POST["template"];
                    
                    //#remove cache of the *complete* site!
                    Wi3::$cache->field_delete_all_wheresite($page->site);
                    //#
                    
                    //save page and return
                    $page->save();
                    echo json_encode(
                        Array(
                            "alert" => "Pagina-eigenschappen van '" . $page->title . "' succesvol gewijzigd!.",
                        )
                    );
                }
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                    )
                );
            }
        }
        
        public function editPageRedirectSettings($pageid) {
            if (is_numeric($pageid) AND !empty($_POST)) {
                $page = ORM::factory("page", $pageid);
                if (Wi3::$rights->check("edit", $page) == true) {
                    
                    $page->page_redirect_type = $_POST["redirect_type"];
                    $page->page_redirect_wi3 = $_POST["redirect_wi3"];
                    $page->page_redirect_external = $_POST["redirect_external"];
                    
                    //save page and return
                    $page->save();
                    echo json_encode(
                        Array(
                            "alert" => "Pagina-eigenschappen van '" . $page->title . "' succesvol gewijzigd!.",
                        )
                    );
                }
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                    )
                );
            }
        }
        
        public function recalculateLanguage($page) {
            //if a page is moved from one language to another, the languagesetting of the page is wrong (still on old language)
            //if that is the case, the language array a page has, should be updated
            $currlang = $page->get_path();
            $currlang = $currlang[0];
            $langarray = Array();
            $langarrayorig = unserialize($page->get("wi3_language_array"));
            foreach($langarrayorig as $name => $post) {
                if(!empty($post)) {
                    if ($post == $page->id) { 
                        //if we encounter ourselves, the $name is ALWAYS our own language
                        unset($langarray[$name]);
                        $name = $currlang->id;
                    }
                    $langarray[$name] = $post; //set related page-id
                    //now, fetch the related page and set related pages for that page as well...
                    $relatedpage = ORM::factory("page", $post);
                    $relatedlangarray = unserialize($relatedpage->get("wi3_language_array"));
                    if (!is_array($relatedlangarray)) { $relatedlangarray = Array(); }
                    foreach($langarrayorig as $name2 => $post2) {
                        //now only change anything if it has to do with THIS page
                        if ($post2 == $page->id) {
                            //if we encounter ourselves, the $name2 is ALWAYS our own language
                            unset($relatedlangarray[$name2]);
                            $relatedlangarray[$currlang->id] = $page->id; //set related page-id
                        }
                    }
                    //add link to this page as well...
                    //add this page to the related page in this page's language
                    $relatedlangarray[$currlang->id] = $page->id;
                    //save
                    $relatedpage->set("wi3_language_array", serialize($relatedlangarray));
                    $relatedpage->save();
                }
            }
            $langarray[$currlang->id] = $page->id;
            
            //#remove cache of the *complete* site!
            Wi3::cache_field_delete_all_wheresite($page->site);
            //#
                        
            $page->set("wi3_language_array", serialize($langarray));
            $page->save();
        }
        
        public function setPageType($pageid, $type = "") {
            $this->template = View::factory("templates/ajax"); //ajax.php is an empty view. This guarantees hat the page is stripped from any unwanted tag
            
            if (!is_numeric($pageid)) { echo "geen geldige pageid"; return; }
            else {
                $page = ORM::factory("page", $pageid);
                //and get fields
                $falseroot = ORM::factory("field");
                $falseroot->leftnr = 0;
                $falseroot->rightnr = "999999999999999999999999";
                $falseroot->scope = $page->id;
                $imaginaryroot = $falseroot->get_tree($falseroot);
            }
            
            //now fetch the possible page-types
            //and add fields to this page accordingly
            $site = Wi3::$site;
            $prefilled_pages = Wi3::get_prefilled_pages($site);
            if (isset($prefilled_pages[$type])) {
                $pfpage = $prefilled_pages[$type];
            } else if (isset($prefilled_pages["standard"])) {
                $pfpage = $prefilled_pages["standard"];
            }
            
            if (!isset($pfpage["fields"])) {
                $pfpage["fields"] = Array();
            }
            if (!isset($pfpage["title"])) {
                $pfpage["title"] = "standard";
            }
            
            foreach($pfpage["fields"] as $dropzoneid => $fieldtypes) {
                
                //check if dropzone already exists. In that case, don't do anything
                $dropzoneexists = false;
                if (!empty($imaginaryroot->children)) { //if there are dropzones
                    foreach($imaginaryroot->children as $dropzone) {
                        if ($dropzone->type == $dropzoneid) { //'type' is used as a dropzone-id
                            $dropzoneexists = true;
                            break;
                        }
                    }
                }
                if ($dropzoneexists == false) {
                    //adding fields to this dropzone
                    //(if dropzone does not exist, it will be created, so that's all right)
                    ///adding is from bottom to top, but description is from top to bottom
                    $fieldtypes = array_reverse($fieldtypes);    //so reverse the array
                    foreach($fieldtypes as $fieldtype) {
                        $_POST["type"] = $fieldtype;
                        ob_start();
                        $this->addFieldToDropZone($page->id, $dropzoneid); //cache is deleted in addFieldToDropZone
                        ob_end_clean();
                    }
                }
                
            }
            
            echo json_encode(
                Array(
                    "alert" => "Type van pagina " .$page->title . " is gewijzigd naar " . $pfpage["title"] . ".",
                    "dom" => Array("append" => Array("#menu_pages" => "<li class='treeItem' id='treeItem_" . $page->id . "'><span>" . html::anchor("engine/content/" . $page->id, $page->title) . "</span></li>")),
                    "scriptsafter" => Array(
                        "updatetree" => "menu_pages_tree();",
                        "editname"  => "wi3_edit_page_settings('#treeItem_" . $page->id . " > span > a');"
                    )
                )
            );

        }
        
        //-------------------------------------------------------------
        // files
        //-------------------------------------------------------------
        public function moveFileBefore($movedfile, $referencefile) {
            $fileid = substr($movedfile,9);
            $refid = substr($referencefile,9);
            if (Wi3::$files->moveBefore($fileid, $refid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "bestand is verhuisd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "bestand kon NIET verhuisd worden"
                    )
                );
            }
        }
        
       public function moveFileAfter($movedfile, $referencefile) {
            $fileid = substr($movedfile,9);
            $refid = substr($referencefile,9);
            if (Wi3::$files->moveAfter($fileid, $refid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "bestand is verhuisd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "bestand kon NIET verhuisd worden"
                    )
                );
            }
        }
        
        public function moveFileUnder($movedfile, $referencefile) {
            $fileid = substr($movedfile,9);
            $refid = substr($referencefile,9);
            if (Wi3::$files->moveUnder($fileid, $refid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "bestand is verhuisd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "bestand kon NIET verhuisd worden"
                    )
                );
            }
        }
        
        public function deleteFile($filename) {
            $fileid = substr($filename,9);
            if (Wi3::$files->delete($fileid)) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                echo json_encode(
                    Array(
                        "alert" => "bestand is verwijderd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "bestand kon NIET verwijderd worden"
                    )
                );
            }
        }
        
        public function startEditFileSettings($filename) {
            $fileid = substr($filename,9);
            $editview = View::factory("wi3/ajax/files_file_settings");
            $editview->site = Wi3::$site;
            if (is_numeric($fileid) AND !empty($fileid)) {
                $file = ORM::factory("file", $fileid);
                if (!empty($file->id)) {
                    //start to edit file
                    $editview->file = $file;
                    echo json_encode(
                        Array(
                            "scriptsbefore" => Array("$('#files_filesettings_tabs').hide();"),
                            "dom" => Array(
                                "fill" => Array("#files_filesettings_tabs" => $editview->render() )
                            ),
                            "scriptsafter" => Array("workplace.files_filesettings_enable();", "$('#files_filesettings_tabs').show();", "$('#files_filename').focus()"),
                        )
                    );
                }
            }
        }
        
        public function editFileSettings($fileid) {
            if (is_numeric($fileid) AND !empty($_POST)) {
                $file = ORM::factory("file", $fileid);
                if (Wi3::$rights->check("edit", $file) == true) {
                    
                    $oldname = $file->filename;
                    
                    foreach($_POST as $name => $post) {
                        if ($name == "title") { 
                            $file->title = $post;
                        } else if ($name == "page_visibility") {
                            $file->visible = true;
                        } else if ($name == "viewright" OR $name == "editright" OR $name == "adminright") {
                            //check for admin privileges
                            if (Wi3::$rights->check("admin", $file)) {
                                //if the viewright is changed, it might be necessary to move the file around on the disk
                                //if there is no right needed: put (or keep) it in the /files folder
                                //if there is special right required: put (or keep) it in the /files/protected folder. If it is in the protected folder, it will be rendered through a right-checker (siteresourceloader controller)
                                if ($name == "viewright") {
                                    if (empty($post) AND file_exists(Wi3::$pathof->site . "data/files/protected/" . $file->filename)) {
                                        //if there is no required role, and the file was in the protected folder, move it to the unprotected folder
                                        copy(Wi3::$pathof->site . "data/files/protected/" . $file->filename, Wi3::$pathof->site . "data/files/" . $file->filename);
                                        unlink(Wi3::$pathof->site . "data/files/protected/" . $file->filename);
                                    } else if (!empty($post) AND file_exists(Wi3::$pathof->site . "data/files/" . $file->filename)) {
                                        //if there is a required role, and the file was in the unprotected folder, move it to the protected folder
                                        copy(Wi3::$pathof->site . "data/files/" . $file->filename, Wi3::$pathof->site . "data/files/protected/" . $file->filename);
                                        unlink(Wi3::$pathof->site . "data/files/" . $file->filename);
                                    }
                                }
                                $file->$name = $post;
                            }
                        }
                    }
                    
                    //#remove cache of the *complete* site!
                    $site = Wi3::$site; //we can be sure that user is logged in, so that Wi3::$site is set
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    $file->save();
                    echo json_encode(
                        Array(
                            "alert" => "Eigenschappen van '" . $oldname . "' succesvol gewijzigd!",
                            "dom" => Array(
                                "fill" => Array("#treeItem_" . $fileid   . " > span > a" => $file->title)
                            ),
                            "scriptsbefore" => Array("workplace.menu_editdiv_hide()")
                        )
                    );
                }
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Pagina-eigenschappen konden NIET gewijzigd worden."
                    )
                );
            }
        }
        
        //-------------------------------------------------------------
        // users
        //-------------------------------------------------------------
        public function addUserToSite() {
            //user is logged in... now we need to check whether he has siteadmin rights for this site
            if (!empty($_POST["newuser_username"]) AND !empty($_POST["newuser_password"]) AND Wi3::$rights->check("admin", Wi3::$site)) {
                //add user to this site
                $user = ORM::factory("user");
                $user->username = $_POST["newuser_username"];
                $user->email = "random" . rand(10,99999) . "@random.com";
                $user->password = $_POST["newuser_password"];
                
                //create a unique role for this user so that pages/files etc can be targeted towards just one single user
                $uniquerole = ORM::factory('role', '_' . $user->username);
                if ($uniquerole->name == null) {
                    //this role does not exist yet
                    $uniquerole->name = '_' . $user->username;
                    $uniquerole->description = 'Unique role for user ' . $user->username;
                    $uniquerole->save();
                    $user->add($uniquerole); //relation will be saved below with $user->save()
                }
                else
                {
                    // Does exist, but the user should still be coupled
                    $user->add($uniquerole); //relation will be saved below with $user->save()
                }
                
                //ORM::factory('role', 'login') returns orm object
                //$user->add creates a relation between $user and role orm model returned by ORM::factory('role', 'login')
                if ($user->add(ORM::factory('role', 'login')) AND $user->save())
                {
                    //now also add this user to the current site
                    $site = Wi3::$site;
                    $li = html::anchor("engine/users/", $user->username);
                    if ($user->add($site) AND $user->save()) {
                        echo json_encode(
                            Array(
                                "alert" => "gebruiker '" . $_POST["newuser_username"] . "' is aangemaakt",
                                "scriptsafter" => Array(
                                    "workplace.currentTree().addNode('treeItem_" . $user->id . "','" . addslashes($li) . "')", 
                                    "editname"  => "wi3_edit_user_settings('#treeItem_" . $user->id . " > span > a');"
                                )
                            )
                        );
                    }
                }
            }
        }
        
        public function startEditUserSettings($id) {
            $id = substr($id,9);
            $editview = View::factory("wi3/ajax/users_user_settings");
            $editview->site = Wi3::$site;
            if (is_numeric($id) AND !empty($id)) {
                $user = ORM::factory("user", $id);
                if (!empty($user->id)) {
                    //start to edit
                    $editview->user = $user;
                    echo json_encode(
                        Array(
                            "scriptsbefore" => Array("$('#users_usersettings_tabs').hide();"),
                            "dom" => Array(
                                "fill" => Array("#users_usersettings_tabs" => $editview->render() )
                            ),
                            "scriptsafter" => Array("workplace.users_usersettings_enable();", "$('#users_usersettings_tabs').show();", "$('#users_username').focus()"),
                        )
                    );
                }
            }
        }
        
        public function editUserSettings($id) {
            if (is_numeric($id) AND !empty($_POST)) {
                $user= ORM::factory("user", $id);
                //check if the meant user is user of the site of the logged in user
                $belongstosamesite = false;
                foreach($user->sites as $site) {
                    if ($site->id == Wi3::$site->id) {
                        $belongstosamesite = true;
                        break;
                    }
                }
                //if you are admin of the site, you can edit user roles
                //however, only for users of that very site!
                if ($belongstosamesite AND Wi3::$rights->check("admin", Wi3::$site) == true) {
                    
                    $oldname = $user->username;
                    
                    foreach($_POST as $name => $post) {
                        if ($name == "username") { 
                            $user->username = $post;
                        } /*else if ($name == "page_visibility") {
                            $file->visible = true;
                        } else if ($name == "viewright" OR $name == "editright" OR $name == "adminright") {
                            //check for admin privileges
                            if (Wi3::$rights->check("admin", $file)) {
                                //if the viewright is changed, it might be necessary to move the file around on the disk
                                //if there is no right needed: put (or keep) it in the /files folder
                                //if there is special right required: put (or keep) it in the /files/protected folder. If it is in the protected folder, it will be rendered through a right-checker (siteresourceloader controller)
                                if ($name == "viewright") {
                                    if (empty($post) AND file_exists(Wi3::$pathof->site . "data/files/protected/" . $file->filename)) {
                                        //if there is no required role, and the file was in the protected folder, move it to the unprotected folder
                                        copy(Wi3::$pathof->site . "data/files/protected/" . $file->filename, Wi3::$pathof->site . "data/files/" . $file->filename);
                                        unlink(Wi3::$pathof->site . "data/files/protected/" . $file->filename);
                                    } else if (!empty($post) AND file_exists(Wi3::$pathof->site . "data/files/" . $file->filename)) {
                                        //if there is a required role, and the file was in the unprotected folder, move it to the protected folder
                                        copy(Wi3::$pathof->site . "data/files/" . $file->filename, Wi3::$pathof->site . "data/files/protected/" . $file->filename);
                                        unlink(Wi3::$pathof->site . "data/files/" . $file->filename);
                                    }
                                }
                                $file->$name = $post;
                            }
                        }*/
                    }
                    
                    //#remove cache of the *complete* site!
                    $site = Wi3::$site; //we can be sure that user is logged in, so that Wi3::$site is set
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    $user->save();
                    echo json_encode(
                        Array(
                            "alert" => "Eigenschappen van '" . $oldname . "' succesvol gewijzigd!",
                            "dom" => Array(
                                "fill" => Array("#treeItem_" . $id   . " > span " => $user->username)
                            )
                        )
                    );
                } else {
                    echo json_encode(
                        Array(
                            "alert" => "Gebruikers-eigenschappen konden NIET gewijzigd worden."
                        )
                    );
                }
            }
        }
        
        public function revokeUserRole() {
            $user = ORM::factory("user", $_POST["userid"]);
            $belongstosamesite = false;
            foreach($user->sites as $site) {
                if ($site->id == Wi3::$site->id) {
                    $belongstosamesite = true;
                    break;
                }
            }
            //if you are admin of the site, you can edit user roles
            //however, only for users of that very site!
            if ($belongstosamesite AND Wi3::$rights->check("admin", Wi3::$site) == true) {
                $role = ORM::factory("role", $_POST["roleid"]);
                //revoke the role
                $user->remove($role);
                $user->save();
                echo json_encode(
                    Array(
                        "alert" => "Rol succesvol losgekoppeld!",
                        "dom" => Array(
                            "remove" => Array("#role_".$role->id)
                        )
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Gebruikers-eigenschappen konden NIET gewijzigd worden."
                    )
                );
            }
        }
        
        public function addUserRole() {
            $user = ORM::factory("user", $_POST["userid"]);
            $belongstosamesite = false;
            foreach($user->sites as $site) {
                if ($site->id == Wi3::$site->id) {
                    $belongstosamesite = true;
                    break;
                }
            }
            //if you are admin of the site, you can edit user roles
            //however, only for users of that very site!
            if ($belongstosamesite AND Wi3::$rights->check("admin", Wi3::$site) == true) {
                $role = ORM::factory("role", $_POST["rolename"]);
                if ($role->name == null) {
                    //role does not yet exist
                    $role->name = $_POST["rolename"];
                    $role->save();
                }
                $user->add($role);
                $user->save();
                //make userrole list
                $listhtml = "";
                $counter = 0;
                foreach($user->roles as $role) {
                    $counter++;
                    $listhtml .= "<tr name='role_" . $role->id . "' id='role_" . $role->id . "'><td>" . $counter . ".</td><td><span>" . $role->name . "</span></td><td><a href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/revokeUserRole/\", {userid: " . $user->id . ", roleid: " . $role->id . "}); return false;'>ontkoppelen van gebruiker</a></td></tr>";
                }
                echo json_encode(
                    Array(
                        "alert" => "Rol succesvol toegevoegd!",
                        "dom" => Array(
                            "fill" => Array("#userroles_list" => $listhtml)
                        )
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "Gebruikers-eigenschappen konden NIET gewijzigd worden."
                    )
                );
            }
        }
        
        public function removeUser() {
            $user = ORM::factory("user", $_POST["userid"]);
            $belongstosamesite = false;
            foreach($user->sites as $site) {
                if ($site->id == Wi3::$site->id) {
                    $belongstosamesite = true;
                    break;
                }
            }
            //if you are admin of the site, you can edit user roles
            //however, only for users of that very site!
            if ($belongstosamesite AND Wi3::$rights->check("admin", Wi3::$site) == true) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                //#
                // Revoke user roles
                foreach($user->roles as $role)
                {
                    $user->remove($role);
                    $user->save(); // reflect the changes in the DB
                }
                // Remove user 
                $user->delete();
                echo json_encode(
                    Array(
                        "alert" => "gebruiker is verwijderd"
                    )
                );
            } else {
                echo json_encode(
                    Array(
                        "alert" => "gebruiker kon NIET verwijderd worden"
                    )
                );
            }
        }
        
        public function changePassword() {
            $user = ORM::factory("user", $_POST["userid"]);
            $belongstosamesite = false;
            foreach($user->sites as $site) {
                if ($site->id == Wi3::$site->id) {
                    $belongstosamesite = true;
                    break;
                }
            }
            //if you are admin of the site, you can edit user roles
            //however, only for users of that very site!
            if ($belongstosamesite AND Wi3::$rights->check("admin", Wi3::$site) == true) {
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite(Wi3::$site);
                $user->password = $_POST["password"];
                
                if ($user->save())
                {
                   echo json_encode(
                        Array(
                            "alert" => "De gebruiker <strong>" . $user->username .  "</strong> heeft nu wachtwoord <strong>" . $_POST["password"] . "</strong>"
                        )
                    );
                }
            } else {
               echo json_encode(
                    Array(
                        "alert" => "Wachtwoord kon NIET aangepast worden"
                    )
                );
            }
        }

         
        
        //## CUSTOM FUNCTIONS ############
        //Functions that sometimes need to be called for maintenance 
        public function createpageurls() {
            $site = Wi3::$user->sites[0];
            //loop through every page and create an url from the pagename
            //this happens automatically if we set a name on the page, so the only thing we need to do is set the name
            foreach($site->pages as $page) {
                $page->title = $page->title;
                $page->save();
            }
        }
        
        //this functions makes sure that any views are loaded in the Wi3::$template namespace
        //so the $this in these views refers to Wi3::$template
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            //we want the pagetemplate (and other templates) to be available through the Wi3_template namespace
            return Wi3::$template->_kohana_load_view($kohana_view_filename, $kohana_input_data);
        }
        
    }

?>
