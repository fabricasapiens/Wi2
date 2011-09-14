<?php

    class Wi3_files {
        //returns a list of ORM objects, flat, not treewise
        static function find($filteroptions = Array()) {
            $file = ORM::factory("file");
            $site = Wi3::$site;
            
            $query = $file->where("scope", $site->id);
            
            //FILTERING
            $filterquery = " AND scope ='" . $site->id . "' ";
            //check filteroptions
            if (isset($filteroptions["whereExt"]) AND !empty($filteroptions["whereExt"])) {
                if (!is_array($filteroptions["whereExt"])) {
                    $filteroptions["whereExt"] = Array($filteroptions["whereExt"]);
                }
                $filterquery .= " AND (";
                foreach($filteroptions["whereExt"] as $ext) {
                    $filterquery .= " filename LIKE '%." . $ext . "' OR ";
                }
                $filterquery = substr($filterquery, 0, -3) . ")"; //strip last OR and add ')' for closing the AND clausule
                //if we filter on Extension, there will be no folders found. However, if we need to show folders, we will need to add an OR = folder clausule
                if (isset($filteroptions["withFolders"]) AND $filteroptions["withFolders"] == true) {
                    $filterquery = " AND ( " . substr($filterquery, 4) . " OR type = 'folder') ";
                } 
            } else {
                //if we do not need to filter for extensions, folders will be catched too
                //however, if the user does not set to do so, we will hide the folders
                if (!isset($filteroptions["withFolders"]) OR $filteroptions["withFolders"] == false) {
                    //usually, do NOT show folders. If you want a fileList, you do NOT want a folderlist. If you want folders, you can call recursiveFileList for that
                    $filterquery .= " AND type != 'folder' ";
                }
            }
            //whereFolder and whereParent are aliases
            if (isset($filteroptions["whereFolder"]) AND !empty($filteroptions["whereFolder"]) AND is_numeric($filteroptions["whereFolder"])) { $filteroptions["whereParent"] = $filteroptions["whereFolder"]; }
            if (isset($filteroptions["whereParent"]) AND !empty($filteroptions["whereParent"]) AND is_numeric($filteroptions["whereParent"])) {
                //fetch the parent
                $parent = ORM::factory("file", $filteroptions["whereParent"]);
                $filterquery .= " AND  leftnr > '" . $parent->leftnr . "' AND rightnr < '" . $parent->rightnr . "' ";
            }
 
            if (!empty($filterquery)) { $query->where(substr($filterquery, 4)); } //add filter criteria minus the preceding AND
            $query->orderby('leftnr','ASC'); //order by leftnr
            $files = $query->find_all()->as_array(); //fetch all things
            //DONE FILTERING
            
            //go through files and add the URL and filelocation parameter
            $filefolderlocation = Wi3::$pathof->site . "data/files/";
            $filefolderurl = Wi3::$urlof->site . "data/files/";
            foreach($files as $retfile) { //could keep complete reference to object intact by placing the & before $retfile
                //filename, leftnr and rightnr should be set. Otherwise, just delete the file
                if (empty($retfile->filename) OR empty($retfile->leftnr) OR empty($retfile->rightnr)) {
                    //delete object. This cannot be done with the leftnr or rightnr but just by id
                    Database::instance()->from($file->table_name)->where("id", $retfile->id)->delete();
                }
                $retfile->filelocation = $filefolderlocation . $retfile->filename;
                $retfile->url = $filefolderurl . $retfile->filename;
            }
            
            //return
            return $files;
        }
        
        //returns a list of ORM objects, treewise, not flat
        static function findRecursive($filteroptions = Array()) {
            $site = Wi3::$site;
            
            $filteroptions["withFolders"] = true; //we do want Folders included, otherwise creating a tree is not even possible
            $files = self::find($filteroptions);
            
            //if there are results
            if(count($files)>0)
            {
                
                //set initial parent, if there was a filteroption for that
                if (isset($filteroptions["whereFolder"]) AND !empty($filteroptions["whereFolder"]) AND is_numeric($filteroptions["whereFolder"])) { $filteroptions["whereParent"] = $filteroptions["whereFolder"]; }
                if (isset($filteroptions["whereParent"]) AND !empty($filteroptions["whereParent"]) AND is_numeric($filteroptions["whereParent"])) {
                    //fetch the parent
                    $root = ORM::factory("file", $filteroptions["whereParent"]);
                } else {
                    $root = ORM::factory("file"); //files do not have a parent , so are initially in root. Create an imaginary root
                    $root->scope = $site->id;
                    $root->leftnr = 0;
                    $root->rightnr = 99999999999999999;
                }

                $parent =  $root;
                $parent->children = Array();
                
                //check whether we need to count the amount of FileChildren
                $countFileDescendants = isset($filteroptions["countFileDescendants"]) AND $filteroptions["countFileDescendants"] == true;
                if ($countFileDescendants) 
                    $parent->fileDescendantsAmount = 0;
                
                $highestRightnr = 0;
                foreach($files as $child_data)
                {
                    
                    //set the fileDescendantsAmount (initially 0) if it is not already
                    $child = $child_data;
                    if ($child->rightnr > $highestRightnr) { $highestRightnr = $child->rightnr; }
                    $child->children=array();
                    if ($countFileDescendants) {
                        if ($child->__isset("fileDescendantsAmount") == false) { $child->__SET("fileDescendantsAmount",0); }
                    }
                    
                    //$parent is the previously processed file
                    //if the current (next) file is NOT a descendant of the $parent, 
                    //then the next file is a sibling or a file in a tree that has split off some levels 'above'
                    //then bubble up from $parent->parent etc until we have found the file that is ancestor of both $child (then the while condition fullfils) and the initial $parent (the $parent we were bubbling up from there)
                    //it is at that level that the tree of $child and the previous $child have been split off (thus are siblings)
                    //
                    while(!$child->is_descendant_of($parent))  {
                        if (isset($parent->parent)) {
                            if ($countFileDescendants) {
                                //bubbling up in the tree, and telling our parents about how many fileChildren there are
                                $parent->parent->fileDescendantsAmount = $parent->parent->fileDescendantsAmount + $parent->fileDescendantsAmount;
                            }
                            $parent = $parent->parent;
                        } else {
                            break;
                        }
                    }     
                    $child->parent=$parent;
                        
                    $parent->children[]=$child;
                    if ($countFileDescendants) {
                        //add this file to fileChildrenAmount of the parent
                        if ($child->type == "file") {
                            $child->parent->fileDescendantsAmount = $child->parent->fileDescendantsAmount + $child->fileDescendantsAmount + 1;
                        }
                    }
                    $parent=$child;
                    
                }
                //for last time bubbling up and adding proper amount of fileChildrenAmount
                if ($countFileDescendants) {
                    $child = ORM::factory("file");
                    $child->leftnr = $highestRightnr + 1;
                    $child->rightnr = $highestRightnr + 2;
                    while(!$child->is_descendant_of($parent)) {
                        if (isset($parent->parent)) {
                            $parent->parent->fileDescendantsAmount = $parent->parent->fileDescendantsAmount + $parent->fileDescendantsAmount;
                            $parent = $parent->parent;
                        } else {
                            break;
                        }
                    } 
                }
            } else {
                //there are no files
                //create a false root and leave it at that
                $root = ORM::factory("file"); //files do not have a parent , so are initially in root. Create an imaginary root
                $root->scope = $site->id;
                $root->leftnr = 0;
                $root->rightnr = 99999999999999999;
            }
            
            if (isset($filteroptions["returnRoot"]) AND $filteroptions["returnRoot"] == true) {
                return $root;
            } else {
                return $root->children;
            }
        }
        
        //-------------------------------------------------------------
        // functions to move files around or to delete them
        //-------------------------------------------------------------
        public function moveBefore($file, $reffile) {
            //create ORM objects if just IDs are given
            if (is_numeric($file)) {
                $file = ORM::factory("file", $file);
            }
            if (is_numeric($reffile)) {
                $reffile = ORM::factory("file", $reffile);
            }
            //check rights on the files
            if (!Wi3::$rights->check("move", $file) OR !Wi3::$rights->check("move", $reffile)) {
                return false;
            }
            
            if ($reffile AND $file) {
                $file->move_to_prev_sibling_of($reffile);
                $file->reload();
                return true;
            }
        }
        
        public function moveAfter($file, $reffile) {
            //create ORM objects if just IDs are given
            if (is_numeric($file)) {
                $file = ORM::factory("file", $file);
            }
            if (is_numeric($reffile)) {
                $reffile = ORM::factory("file", $reffile);
            }
            //check rights on the files
            if (!Wi3::$rights->check("move", $file) OR !Wi3::$rights->check("move", $reffile)) {
                return false;
            }
            
            if ($reffile AND $file) {
                $file->move_to_next_sibling_of($reffile);
                $file->reload();
                return true;
            }
        }
        
        public function moveUnder($file, $reffile) {
            //create ORM objects if just IDs are given
            if (is_numeric($file)) {
                $file = ORM::factory("file", $file);
            }
            if (is_numeric($reffile)) {
                $reffile = ORM::factory("file", $reffile);
            }
            //check rights on the files
            if (!Wi3::$rights->check("move", $file) OR !Wi3::$rights->check("move", $reffile)) {
                return false;
            }
            
            if ($reffile AND $file) {
                $file->move_to_last_child_of($reffile);
                $file->reload();
                return true;
            }
        }
        
        public function delete($file) {
            //create ORM objects if just IDs are given
            if (is_numeric($file)) {
                $file = ORM::factory("file", $file);
            }
            //check rights on the files
            if (!Wi3::$rights->check("delete", $file)) {
                return false;
            }
            
            if ($file) {
                $filename = Wi3::$pathof->site . "data/files/" . $file->filename;
                if (file_exists($filename))
                {
                    echo $filename . "bestaat <br />";
                    unlink($filename);
                }
                // Check all subdirs
                foreach(glob(Wi3::$pathof->site."data/files/*", GLOB_ONLYDIR) as $dir) {
                    $filename = $dir . "/" . $file->filename;
                    if (file_exists($filename))
                    {
                        echo $filename . "bestaat <br />";
                        unlink($filename);
                    }
                }
                $file->delete();
                // Now really delete the file, to prevent a mess!
                return true;
            }
        }
        
    }

?>