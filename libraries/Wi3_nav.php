<?php

    //----------------------------------------
    // this class only generates HTML, no menu 'objects' that then can be rendered or anything like that
    //----------------------------------------
    class Wi3_nav {
        
        //proper navigation-rendering
        public function menu($tree, $currentpage, $attr = Array(), $otheroptions = Array()) {
            $site = Wi3::$site;
            $modules = Wi3::$config->site("modules");
            if (isset($modules["modules"])) {
                $modules = $modules["modules"];
            } else {
                $modules = Array();
            }
            if (isset($otheroptions["currentlevel"])) { $currentlevel = $otheroptions["currentlevel"]; } else { $currentlevel = 0; if (is_object($currentpage)) { $otheroptions["path"] = $currentpage->get_path(); } }
            $path = $currentpage->get_path();
            ob_start();
            echo "<ul>";
            $counter = 0;
            foreach($tree as $menupage) {
                //if current user is not allowed to view this page, also do not show this page in the menu
                if (Wi3::$rights->check("view", $menupage) == false) {
                    continue;
                }
                //only display when page is set to 'visible' AND the choose_visibility module is enabled
                if ($menupage->visible !== 0 OR !isset($modules["page_choose_visibility"]) OR $modules["page_choose_visibility"]["enabled"] === false) { 
                    $counter++;
                    if ($menupage->id == $currentpage->id) { 
                        //attr moet een array zijn
                        $activeattr = Array();
                        if (is_array($attr)) { 
                            $activeattr = $attr;
                        }
                        //als activeclass niet is gegeven, deze toevoegen
                        if (!isset($activeattr["class"])) {
                            $activeattr["class"] = "active"; 
                        }
                    } else {
                        $activeattr = Array();
                        //we can be on the path to the $currentpage, which will get a 'wi3_on_path_to_active' class
                        if (is_object($otheroptions["path"])) {
                            foreach($otheroptions["path"] as $page) {
                                if ($page->id == $menupage->id) {
                                    $activeattr["class"] = "wi3_on_path_to_active";
                                    break;
                                }
                            }
                        }
                    }
                    echo "<li class='wi3_menu_" . $currentlevel . "_" . $counter . (!empty($activeattr["class"]) ? " " . $activeattr["class"] : "") . "'><span>" . html::anchor(Wi3::$urlof->site . ucfirst($menupage->url), $menupage->title, $activeattr) . "</span>";
                    if ($menupage->children) {
                        $otheroptions["currentlevel"] = ($currentlevel+1);
                        echo $this->menu($menupage->children, $currentpage, $attr, $otheroptions );
                    }
                    echo "</li>";
                }
            }
            echo "</ul>";
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        
        //proper navigation-rendering when using a separated-structure (for example a language-based site)
        //the $level argument tells from how 'deep' the partial menu should be created
        //IE: if we have a menu like 
        //Dutch
        //  pag1
        //  pag2
        //    pag3
        //    pag5
        //English
        //  pag4
        //and we are currently in pag3, we could decide to just display the pages within the current language (pag 1,2,3,5) which would be level 1
        //and we could decide to just display pages within the pag2 group, which would be level 2
        public function partialmenu($tree, $currentpage, $attr = Array(), $fromlevel = 1, $amountoflevels=999999999999999, $otheroptions = Array()) {
            ob_start();
            echo "<ul>";
            if (isset($otheroptions["currentlevel"])) { $currentlevel = $otheroptions["currentlevel"] + $fromlevel; } else { $currentlevel = 0 + $fromlevel; if (is_object($currentpage)) { $otheroptions["path"] = $currentpage->get_path(); } }
            //find out if we need to show the menu from level 1+ or just from level 0
            if ($fromlevel == 0) {
                //just start with the whole tree
                $children = $tree;
            } else {
                //start from a certain 1+ level
                //get path to root
                $searchlevel = 0;
                //now find out what level we need to show 
                foreach($currentpage->get_path() as $currobj) {
                    $deepobject = $currobj;
                    //break at the level we need to be (minimum here is 1)
                    //the rendering of the menu will start from that level
                    $searchlevel++;
                    if ($searchlevel == $fromlevel) { break; }
                }
                //fetch all pages on $level
                $objectwithchildren = $deepobject->get_tree($deepobject);
                $children = $objectwithchildren->children;
            }
            if (!is_array($children)) { $children = Array(); }
            $counter = 0;
            foreach($children as $menupage) {
                //if current user is not allowed to view this page, also do not show this page in the menu
                if (Wi3::$rights->check("view", $menupage) == false) {
                    continue;
                }
                //only display when page is set to 'visible' AND the choose_visibility module is enabled
                $modules = Wi3::$site;
                $modules = Wi3::$config->site("modules");
                $modules = $modules["modules"];
                if ($menupage->visible !== 0 OR !isset($modules["page_choose_visibility"]) OR $modules["page_choose_visibility"] == false) { 
                    $counter++;
                    if ($menupage->id == $currentpage->id) { 
                        //attr moet een array zijn
                        $activeattr = Array();
                        if (is_array($attr)) { 
                            $activeattr = $attr;
                        }
                        //als activeclass niet is gegeven, deze toevoegen
                        if (!isset($activeattr["class"])) {
                            $activeattr["class"] = "active"; 
                        }
                    //} else if (in_array($currentpage, $otheroptions["path"])) {
                     //   var_dump($currentpage);
                    } else {
                        $activeattr = Array();
                        //we can be on the path to the $currentpage, which will get a 'wi3_on_path_to_active' class
                        if (is_object($otheroptions["path"])) {
                            foreach($otheroptions["path"] as $page) {
                                if ($page->id == $menupage->id) {
                                    $activeattr["class"] = "wi3_on_path_to_active";
                                    break;
                                }
                            }
                        }
                    }
                    echo "<li class='wi3_menu_" . $currentlevel . "_" . $counter . "'><span>" . html::anchor(Wi3::$urlof->site . ucfirst($menupage->url), $menupage->title, $activeattr) . "</span>";
                    if ($menupage->children AND $amountoflevels-1 > 0) {
                        $otheroptions["currentlevel"] = ($currentlevel+1);
                        echo $this->partialmenu($menupage->children, $currentpage, $attr, 0, $amountoflevels-1, $otheroptions );
                    }
                    echo "</li>";
                }
            }
            echo "</ul>";
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        
    }

?>