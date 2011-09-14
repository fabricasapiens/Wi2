<?php

    class Wi3_rights {
        
        public function check($action, $object) {
            
            //-------------------------------------
            // Site 
            //-------------------------------------
            if (get_class($object) == "Site_Model") {
                //check if current user is the admin for this site
                if($action == "admin") {
                    //however, first check if there is a user at all
                    if (is_object(Wi3::$user)) { 
                        //first check for the 'global' admin. He can do anything. He has REAL POWER mahaha
                        $userroles = wi3::$user->roles;
                        $hasrole = false;
                        foreach($userroles as $role) {
                            if ($role->name == "admin") {
                                //admin is allowed to do anything
                                return true;
                            } else if($role->name == "siteadmin") {
                                $hasrole = true;
                                break;
                            }
                        }
                        if ($hasrole== false) {
                            //user does not posess the site-admin role
                            return false;
                        }
                        
                        //now check if this user belongs to this site
                        $usersites = Wi3::$user->sites;
                        foreach($usersites as $site) {
                            if ($site->id == $object->id) {
                                return true;
                            }
                        }                     
                        //if we get here, there are no other possibilities to be admin for the site
                        return false;
                    } else {
                        //if there is not even a user, he can also not be an admin
                        return false;
                    }
                } else {
                    //every other action that can not be checked for, is not allowed
                    return false;
                }
            //-------------------------------------
            // Page and File
            //-------------------------------------
            } else if (get_class($object) == "Page_Model" OR get_class($object) == "File_Model") {
                //check whether the logged in user has the role that is needed for a certain action
                if ($action == "add") {
                    //you can do this when you're logged in to a certain site
                    if (is_object(Wi3::$user)) { 
                        return true;
                    } else {
                        return false;
                    }
                } else if($action == "move") {
                    //check if this page can be moved
                    //for now, this is only possible if the user is admin of the site
                    return self::check("admin", $object);
                    /*
                    if (is_object(Wi3::$user)) { 
                        //$neededrole = $object->moveRight;
                        //$userroles = wi3::$user
                        //if (in_array($neededrole, $userroles) { }
                        if ($object->site_id == Wi3::$site->id) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }*/
                } else if($action == "edit") {
                    //check if this page can be edited
                    //therefore, we first need to know the required role of the page for editing
                    $neededrole = $object->editright;
                    if (!empty($neededrole)) {
                        if (is_object(Wi3::$user)) { 
                            $userroles = wi3::$user->roles;
                            $hasrole = false;
                            foreach($userroles as $role) {
                                if ($role->name == "admin") {
                                    return true;    //allowed to do anything
                                }
                                if ($role->name == $neededrole OR $role->name == "siteadmin") {
                                    $hasrole = true;
                                    break;
                                }
                            }
                            if ($hasrole == false) {
                                return false;   //user does not posses the required role
                            }
                            //if user has the role, then check if it is allowed to view pages of THIS site, not another one
                            $usersites = Wi3::$user->sites;
                            $hassite = false;
                            foreach($usersites as $site) {
                                if ($site->id == $object->site_id) {
                                    $hassite = true;
                                    break;
                                }
                            }
                            if ($hassite == true) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            //wel een rol benodigd, maar geen gebruiker aanwezig die rollen heeft
                            //het hebben van die benodigde rol is dan helaas onmogelijk
                            return false;
                        }
                    } else {
                        //everybody can view
                        return true;
                    }
                } else if($action == "delete") {
                    //need to be admin in order to delete any page
                    return self::check("admin", $object);
                    /*
                    //check if this page can be moved
                    if (is_object(Wi3::$user)) { 
                        //$neededrole = $object->moveRight;
                        //$userroles = wi3::$user
                        //if (in_array($neededrole, $userroles) { }
                        if ($object->site_id == Wi3::$site->id) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                    */
                } else if($action == "view") {
                    //check if this page can be viewed by the current viewer
                    //therefore, we first need to know the required 'role' of the page for viewing
                    $neededrole = $object->viewright;
                    if (!empty($neededrole)) {
                        if (is_object(Wi3::$user)) { 
                            $userroles = wi3::$user->roles;
                            $hasrole = false;
                            foreach($userroles as $role) {
                                if ($role->name == "admin") {
                                    //allowed to do anything
                                    return true;
                                }
                                if ($role->name == $neededrole OR $role->name == "siteadmin") {
                                    $hasrole = true;
                                    break;
                                }
                            }
                            if ($hasrole == false) {
                                //user does not posses the required role
                                return false;
                            }
                            //if user has the role, then check if it is allowed to view pages of THIS site, not another one
                            $usersites = Wi3::$user->sites;
                            $hassite = false;
                            foreach($usersites as $site) {
                                if ($site->id == $object->site_id) {
                                    $hassite = true;
                                    break;
                                }
                            }
                            if ($hassite == true) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            //wel een rol benodigd, maar geen gebruiker aanwezig die rollen heeft
                            //het hebben van die benodigde rol is dan helaas onmogelijk
                            return false;
                        }
                    } else {
                        //everybody can view
                        return true;
                    }
                } else if ($action == "admin") {
                  
                    //there are a couple of possibilities here
                    //first: being admin or siteadmin
                    //second: being the owner of the page
                    //third: having the 'adminright' role
                    
                    //however, first check if there is a user at all
                    if (is_object(Wi3::$user)) { 
                        //now check if this user belongs to this site
                        $usersites = Wi3::$user->sites;
                        $hassite = false;
                        foreach($usersites as $site) {
                            if ($site->id == $object->site_id) {
                                $hassite = true;
                                break;
                            }
                        }
                        if ($hassite == false) {
                            return false;
                        }
                        //if we get here, the user 'belongs' to this site, not another
                        
                        //check if the user is the owner of the page
                        if (Wi3::$user->id == $object->user_id) {
                            return true;
                        }
                     
                        //now look at roles
                        $neededrole = $object->adminright;
                        $userroles = wi3::$user->roles;
                        $hasrole = false;
                        foreach($userroles as $role) {
                            if ($role->name == "admin" OR $role->name == "siteadmin") {
                                //admins and site-admins allowed to do admin-related stuff
                                return true;
                            }
                            if ($role->name == $neededrole) {
                                //if the user is the 'admin' for this page
                                return true;
                            }
                        }
                        //if we get here, there are no other possibilities to be admin for this page
                        return false;
                    } else {
                        //if there is not even a user, he can also not be an admin
                        return false;
                    }
                    
                }
            }
            
            //if nothing found, always return false
            return false;
            
        }
        
        public function hasrole($checkrole)
        {
            // First check if there is a user at all
            if (is_object(Wi3::$user)) { 
                $userroles = Wi3::$user->roles;
                $hasrole = false;
                foreach($userroles as $role) {
                    if ($role->name == $checkrole) {
                        return true;
                    }
                }
                return false;
            }
            else
            {
                // No user, so no roles
                return false;
            }
        }
        
    }

?>