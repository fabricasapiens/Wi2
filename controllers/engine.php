<?php

    //-------------------------------------------------------------
    // This controller contains all the pages of the workplace.
    // that is: controlpanel, menu, content and files
    // Any AJAX action that takes place, is handled by the ajaxengine controller
    //-------------------------------------------------------------
    
    //you need to be logged in to view any of this controller's pages,
    //so extend with Login_Controller
    class Engine_Controller extends Login_Controller {
      
        public $template = "wi3/workplace";
        
        public function __construct() {
            parent::__construct();
            //now run the event that page and site are loaded
            //Wi3_pathof and Wi3_urlof hook into this to fetch the path of the current site and of the pagefiller
            if (isset(Wi3::$site)) {
                Event::run("wi3.siteandpageloaded");
            }
            //every page of the workplace does have the navigation on the left
            //and the 'logged in as' and 'requests' on the right hand side
            $this->template->navigationleft = View::factory("wi3/partial/workplacenavigationleft");
            $this->template->navigationright = View::factory("wi3/partial/workplacenavigationright");
        }
      
        public function index() {
            return $this->controlpanel();
        }
        
        //-------------------------------------------------------------
        // The control page, or 'welcome page'
        //-------------------------------------------------------------
        public function controlpanel() {

            $this->template->navigationleft->navigation_active = "controlpanel"; //this will get highlighted  
            $this->template->contentclass = "controlpanel";  //content div will get this class
            
            //create a site for the user if there dos not yet exist one
            $sites = Wi3::$user->sites;
            if (count($sites) == 0) {

                //first fetch the possible page-templates
                $templatedir = Wi3::$pathof->sitetemplates;
                $files = glob($templatedir . "*.php");
                foreach($files as $filename) {
                    $templatename = substr($filename, strrpos($filename, "/")+1, -4);
                    break; //first result is ok, we will use that pagetemplate for creating the first page of this new site
                }
                
                //create site if none exists
                $site = ORM::factory("site");
                $site->title = "site of " . Wi3::$user->username;
                $site->url = Wi3::$user->username;
                $site->default_page_templatetype = "wi3";
                $site->default_page_template = $templatename;
                $site->save();
                Wi3::$site = $site;
                Event::run("wi3.siteloaded"); //The Pathof department will hook into this and make the path to the site available. That path is to be used in the Config-fetching below
                
                Wi3::$user->add($site); //add site to the current user
                Wi3::$user->save();
                
                $pagetypesfile = Wi3::$config->site("pagetypes");
                $pagetypes = $pagetypesfile["pagetypes"];
                $pagetype = $pagetypes[0];
                
                ob_start();
                Ajaxengine::addPage($pagetype);
                ob_end_clean();
                 
            }
            
            //load controlpanel views
            $this->template->content = View::factory("wi3/partial/leftright"); //will divide into left and right section
            $this->template->content->left = View::factory("wi3/partial/controlpanel_left");
            $this->template->content->right = View::factory("wi3/partial/controlpanel_right");
            
            //Title of site
            $site = Wi3::$site;
            $changetitle = "<input value='" . $site->title . "' id='sitetitle' /> <button onClick='wi3.request(\"ajaxengine/changeSiteTitle/\" + $(\"#sitetitle\").val());'>Wijzig</button>";
            $this->template->content->right->changetitle = $changetitle;
            
            //TEMPLATE BOX
            //show the different types of page_templates
            //both of the users' site and the standard templates
            $usertemplatedir = Wi3::$pathof->sitetemplates;
            $usertemplates = "";
            $files = glob($usertemplatedir . "*.php");
            foreach($files as $filename) {
                $templatename = substr($filename, strrpos($filename, "/")+1, -4);
                if ($site->default_page_templatetype == "user" AND $site->default_page_template == $templatename) {
                    $usertemplates .= "<a class='bold' id='template_user_" . $templatename .  "' href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/useUserTemplate/" . $templatename . "\", {});'>" . $templatename . "</a> ";
                } else {
                    $usertemplates .= "<a id='template_user_" . $templatename .  "' href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/useUserTemplate/" . $templatename . "\", {});'>" . $templatename . "</a> ";
                }
            }
            $wi3templatedir = Wi3::$pathof->wi3templates;
            $wi3templates = "";
            $files = glob($wi3templatedir . "*.php");
            foreach($files as $filename) {
                $templatename = substr($filename, strrpos($filename, "/")+1, -4);
                if ($site->default_page_templatetype == "wi3" AND $site->default_page_template == $templatename) {
                    $wi3templates .= "<a class='bold' id='template_wi3_" . $templatename .  "' href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/useWi3Template/" . $templatename . "\", {});'>" . $templatename . "</a> ";
                } else {
                    $wi3templates .= "<a id='template_wi3_" . $templatename .  "' href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/useWi3Template/" . $templatename . "\", {});'>" . $templatename . "</a> ";
                }
            }
            
            $this->template->content->right->usertemplates = $usertemplates;
            $this->template->content->right->wi3templates = $wi3templates;

        }
        
        //-------------------------------------------------------------
        // The page in which the user can edit the menu-structure and properties of pages
        // This editing of properties is done by ajax however, so is to be found in the ajaxengine controller
        //-------------------------------------------------------------
        public function menu() {
            $this->template->navigationleft->navigation_active = "menu"; //this will get highlighted  
            $this->template->content = View::factory("wi3/partial/menu");
            $this->template->contentclass = "menu";
            
            //we need the UI plugin
            Wi3::$plugins->load("plugin_jquery_1_3_2_ui");
            
            $site = Wi3::$site;
            
            //###GET PAGES OF SITE
            $falseroot = ORM::factory("page");
            $falseroot->leftnr = 0;
            $falseroot->rightnr = "999999999999999999999999";
            $falseroot->scope = $site->id;
            $imaginaryroot = $falseroot->get_tree($falseroot);
            //get_tree returns root with in the ->children property all the children recursively
            
            //###LOAD VIEWS
            $this->template->content->imaginaryroot = $imaginaryroot;
            
            //###ADD PAGES BOX
            //load config file with pagetypes
            $pagetypesfile = Wi3::$config->site("pagetypes");
            $pagetypes = $pagetypesfile["pagetypes"];
            $this->template->content->pagetypes = $pagetypes;
            
        }
        
        //-------------------------------------------------------------
        // The page with the Iframe in which the user can edit the page itself
        // The editing operation (displaying of editing page etc) is however executed by the pagefiller in an Iframe
        // See the function "edit_page" for how the pagefiller is called for to execute this task
        //-------------------------------------------------------------
        public function content($pageid = -1) {
            $this->template->navigationleft->navigation_active = "content"; //this will get highlighted  
            //$this->template->title = "";
            
            $site = Wi3::$site;
            $modules = $site->modules; //always returns an Array
            
            //fetch page
            if (empty($pageid)) {
                //if there's no page, then get the root page
                $page = $site->pages[0];
                if (is_object($page)) {
                    $page = $page->get_root($page);
                    //if the site is multilanguage, we want to have NOT the root page, but the first CHILD of the root page
                    if (isset($modules["site_multilanguage"]) AND $page->has_descendants()) {
                        $children = $page->get_children(true);
                        $page = $children[0];
                    }
                }
            } else {
                //if name is set, then load the correct page
                if (is_numeric($pageid)) {
                    $page = ORM::factory("page")->where("id", $pageid)->where("site_id", $site->id)->find();
                } else {
                    $page = ORM::factory("page")->where("url", $pageid)->where("site_id", $site->id)->find();
                }
               
                //if failed, get the first root page anyway
                if (!is_object($page) OR $page->id == 0) {
                    $page = $site->pages[0];
                    if (is_object($page)) {
                        $page = $page->get_root($page);
                        //if the site is multilanguage, we want to have NOT the root page, but the first CHILD of the root page
                        if (isset($modules["site_multilanguage"]) AND $page->has_descendants()) {
                            $children = $page->get_children(true);
                            $page = $children[0];
                        }
                    }
                }
            }
            
            $this->template->totalcontent = View::factory("wi3/partial/content")->set("pageid", $page->id)->render(false);
            $this->template->contentclass = "hidden";
            
        }
        
        //-------------------------------------------------------------
        // The page in which the user can edit the page itself
        // The editing operation (displaying of editing page etc) is however executed by the pagefillre of the page
        //-------------------------------------------------------------
        public function edit_page($pageid = -1) {
            
            //Define that we are in edit-mode
            Wi3::$editmode = true;
            //The individual components need this, in order to determine if they will show some extra edit-hints, some edit-scripts etc
            
            //fetch page
            $page = Wi3::$pages->get_page($pageid);
            Wi3::$page = $page;
            //now run the event that page and site are loaded
            //Wi3_pathof hooks into this to fetch the path of the current site and of the pagefiller            
            Event::run("wi3.siteandpageloaded");
            
            //check rights
            if (Wi3::$rights->check("edit", $page) == true) {
            
                //ok, now load the pagefiller that controls how to display and edit this page
                if (empty($page->page_filler)) { $page->page_filler = "default"; }
                $libname = "Pagefiller_" . strtolower($page->page_filler);
                $pagefiller = new $libname();
                $this->template = $pagefiller->edit_page($page); //a pagefiller should return a View object
                //done
                
            } else if (Wi3::$rights->check("view", $page) == true) {
                //just show the page for viewing
                //Load the pagefiller that controls how to display and edit this page
                //We are NOT in editmode anymore
                Wi3::$editmode = false;
                //The pagefiller can know that we are calling this page from the engine controller, by checking this with Wi3::$routing->controller
                if (empty($page->page_filler)) { $page->page_filler = "default"; }
                $libname = "Pagefiller_" . strtolower($page->page_filler);
                $pagefiller = new $libname();
                $this->template = $pagefiller->view_page(Wi3::$site, $page); //a pagefiller should return a View object
            } else {
                die("U hebt niet het recht deze pagina te wijzigen.");
            }
        }
        
        //-------------------------------------------------------------
        // The page in which the user can upload and manage files
        //-------------------------------------------------------------
        public function files() {
            $this->template->navigationleft->navigation_active = "files"; //this will get highlighted  
            $this->template->content = View::factory("wi3/partial/files");
            $this->template->contentclass = "files";
            
            $site = Wi3::$site;
            
            //we need the UI plugin
            Wi3::$plugins->load("plugin_jquery_1_3_2_ui");
            
            //--------------------
            //add folder, of one is sent along
            //--------------------
            if (isset($_POST["folder"]) AND !empty($_POST["folder"])) {
                
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite($site);
                //#
                
                //create folder
                $file = ORM::factory("file");
                $file->site_id = $site->id;
                $file->scope = $site->id;
                $file->user_id = Wi3::$user->id;
                $file->title = $_POST["folder"];
                $file->type = "folder";
                $file->created = time();
                $file->filename = $_POST["folder"];
                $lastroot = $file->get_last_root();
                if (!is_object($lastroot)) {
                    //if there are no files, then save file as root
                    $file->make_root($file->scope);
                } else {
                    //there are already files present, so add as last file/node
                    $file->insert_as_next_sibling_of($lastroot); //become last node
                }
            }
            
            //--------------------
            //add file, if one is sent along
            //--------------------
            if (isset($_FILES['file'])) {
                //add file
                $filename = basename( $_FILES['file']['name']);
                $extensionpos = strrpos($filename, ".")+1;
                if ($extensionpos > -1) {
                    $badexts = array("php", "phtml", "php3", "phps", "php4", "php5", "asp", "py", "pl");
                    //check for forbidden extensions
                    $ext = substr($filename, $extensionpos);
                    if (in_array(strtolower($ext), $badexts)) {
                        $this->template->content->message = "Het toevoegen van bestanden met dit bestandstype (" . $ext . ") is niet toegestaan. Probeer het nog eens.";
                        return;
                    }
                }
                $target = Wi3::$pathof->site. "data/files/" . basename( $_FILES['file']['name']); 
                if (!file_exists(Wi3::$pathof->site. "data/files")) {
                    if (!mkdir(Wi3::$pathof->site. "data/files")) {
                        $this->template->content->message = "Fout bij wegschrijven van bestand. Dit is een permanente fout. Mail de beheerder van de site met uw probleem.";
                        return;
                    }
                }
                //check if the destination already exist, and if so, change the destination location
                $existscounter = 0;
                while(file_exists($target)) {
                    $existscounter++;
                    $target = Wi3::$pathof->site. "data/files/" . substr($filename, 0, $extensionpos-1) . "_" . $existscounter . "." . substr($filename, $extensionpos);
                }
                ini_set("upload_max_filesize", "50M");
                ini_set('memory_limit', '50M');
                if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                    
                    //#remove cache of the *complete* site!
                    Wi3::$cache->field_delete_all_wheresite($site);
                    //#
                    
                    $message = "Bestand is succesvol geüpload.";
                    $file = ORM::factory("file");
                    $file->site_id = $site->id;
                    $file->scope = $site->id;
                    $file->user_id = Wi3::$user->id;
                    $file->title = basename($_FILES['file']['name']);
                    $file->type = "file";
                    $file->created = time();
                    $file->filename = basename($target);
                    $lastroot = $file->get_last_root();
                    if (!is_object($lastroot)) {
                        //if there are no files, then save file as root
                        $file->make_root($file->scope);
                    } else {
                        //there are already files present, so add as last file/node
                        $file->insert_as_next_sibling_of($lastroot); //become last node
                        //$file->insert_as_first_child_of($lastroot); //become last node
                    }
                } else {
                    $message = "Er ging iets fout bij het uploaden. Probeer het alstublieft opnieuw.";
                }
                $this->template->content->message = $message;
            }
                        
            //--------------------
            // display the files
            //--------------------
            //get files of this site
            $files = Wi3::$files->findRecursive(Array("countFileDescendants" => true,"whereParent" =>0));
            /*
            $falseroot->leftnr = 0;
            $falseroot->rightnr = 999999999999999999999999;
            $falseroot->scope = $site->id;
            $imaginaryroot = $falseroot->get_tree($falseroot);*/
            //get_tree returns root with in the ->children property all the children recursively
            
            $this->template->content->files = $files;
            
        }
        
        public function unpackfile($id) {
            
            $zipfile = ORM::factory("file", $id);
            if ($zipfile->site_id != Wi3::$site->id) { return "u hebt geen rechten om deze actie uit te voeren."; }
            $currentfolder = $zipfile->get_parent();
            
            //unpack file
            try {
                
                $site = Wi3::$site;
                
                //#remove cache of the *complete* site!
                Wi3::$cache->field_delete_all_wheresite($site);
                //#
                
                $z = new ZipArchive();
                if ($z->open(Wi3::$pathof->site. "data/files/" . $zipfile->filename)) {
                    for ($i=0; $i < $z->numFiles; $i++) {
                        $fileinfo = $z->statIndex($i);
                        $filename = $fileinfo["name"];
                        
                        //check for security and then add
                        $extensionpos = strrpos($filename, ".")+1;
                        if ($extensionpos > -1) {
                            $badexts = array("php", "phtml", "php3", "phps", "php4", "php5", "asp", "py", "pl");
                            //check for forbidden extensions
                            $ext = substr($filename, $extensionpos);
                            if (in_array(strtolower($ext), $badexts)) {
                                continue; //skip this file
                            }
                        }
                        $target = Wi3::$pathof->site. "data/files/" . basename($filename); 
                        if (!file_exists(Wi3::$pathof->site. "data/files")) {
                            if (!mkdir(Wi3::$pathof->site. "data/files")) {
                                continue; //skip this file
                            }
                        }
                        //check if the destination already exist, and if so, change the destination location
                        $existscounter = 0;
                        while(file_exists($target)) {
                            $existscounter++;
                            $target = Wi3::$pathof->site. "data/files/" . substr($filename, 0, $extensionpos-1) . "_" . $existscounter . "." . substr($filename, $extensionpos);
                        }
                        
                        //read file from zip and save it
                        $contents = "";
                        $fp = $z->getStream($filename);
                        if(!$fp) { 
                            continue; //next file
                        }
                        while (!feof($fp)) {
                            $contents .= fread($fp, 2);
                        }
                        fclose($fp);
                        file_put_contents($target,$contents);
                          
                         //add file to DB
                        {
                            $file = ORM::factory("file");
                            $file->site_id = $site->id;
                            $file->scope = $site->id;
                            $file->user_id = Wi3::$user->id;
                            $file->title = basename($filename);
                            $file->type = "file";
                            $file->created = time();
                            $file->filename = basename($target);
                            $lastroot = $file->get_last_root();
                            if (!is_object($lastroot)) {
                                //if there are no files, then save file as root
                                $file->make_root($file->scope);
                            } else if (is_object($currentfolder)) {
                                //there are already files present, add to the current folder
                                $file->insert_as_last_child_of($currentfolder); //become node of current folder
                            } else {
                                $file->insert_as_next_sibling_of($lastroot); //become last node in lowest level
                            }
                        }
                    }
                }
                
                
            } catch(Exception $e) { }
            
            //proper redirect the page to the engine/files
            //this also prevents a Page refresh where the file will be unpakced again...
            header("location: " . Wi3::$urlof->wi3 . "engine/files");
        }
        
        public function render_site($pageid = -1) {
            //do NOT load any Wi3 stuff, but just the site as it was meant to be
            //the code below comes mainly from the content() function, as it needs to render the site content as well
            $sites = Wi3::$user->sites;
            $site = $sites[0];
            
            if (!empty($pageid) AND $pageid > -1) {
                //user has selected a page to view               
                //so go ahead and get that page along with its fields
                if (is_numeric($pageid)) {
                    $page = ORM::factory("page")->where("id", $pageid)->where("site_id", $site->id)->find();
                } else {
                    $page = ORM::factory("page")->where("url", urldecode($pageid))->where("site_id", $site->id)->find();
                }
            } else {
                //select the root page of this site
                $page = $site->pages[0];
                $page = $page->get_root($page);
            }
            //ok, now load the pagefiller that controls how to display and edit this page
            if (empty($page->page_filler)) { $page->page_filler = "default"; }
            $libname = "Pagefiller_" . strtolower($page->page_filler);
            $pagefiller = new $libname();
            $pagefiller->user = Wi3::$user;
            $pagefiller->site = $site;
            $pagefiller->page = $page;
            $this->template = $pagefiller->view_page($site, $page); //a pagefiller should return a View object
            //done
            
            //also, we also want the Kohana javascript to be availabel
            //javascript::add('http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js');
            javascript::add('media/javascript/jquery-1.3.1.min.js');
            javascript::add("media/javascript/kohana.js");
            //and to make sure the navigation works properly
            javascript::add("media/javascript/workplace_render_site.js");
            
        }
        
        //function for managing users
        public function users() {
            //user needs to be admin of this site
            if (!Wi3::$rights->check("admin", Wi3::$site)) {
                die("U hebt niet de juiste rechten om gebruikers te beheren.");
            }
            
            $this->template->navigationleft->navigation_active = "users"; //this will get highlighted  
            $this->template->content = View::factory("wi3/partial/users");
            $this->template->contentclass = "users";
            
            //we need the UI plugin
            Wi3::$plugins->load("plugin_jquery_1_3_2_ui");
            
            $site = Wi3::$site;
            
            //get all users of this site
            $users = $site->users;
            $this->template->content->users = $users;
            
            //###ADD PAGES BOX
            //load config file with pagetypes
            $pagetypesfile = Wi3::$config->site("pagetypes");
            $pagetypes = $pagetypesfile["pagetypes"];
            $addpages = "";
            foreach($pagetypes as $index => $pagetype) {
                $addpages .= "<a href='javascript:void(0);' onClick='wi3.request(\"ajaxengine/addPage/" . $index . "/\" + workplace.currentTree().getSelected().attr(\"id\") , {})'>" . ucfirst($pagetype["title"])  .  "</a><br />";
            }
            $this->template->content->addpages = $addpages;
            
        }
        
        //## ADMIN FUNCTIONS ####################
        //user management
        public function create_user($username, $password) {
            if (Auth::instance()->logged_in("admin")) {
                $user = ORM::factory("user");
                $user->username = $username;
                $user->email = "random" . rand(10,99999) . "@random.com";
                $user->password = $password;
                
                //ORM::factory('role', 'login') returns orm object and get's value from colum with name=login
                //$user->add creates a relation between $user and role orm model returned by ORM::factory('role', 'login')
                if ($user->add(ORM::factory('role', 'login')) AND $user->add(ORM::factory('role', 'siteadmin')) AND $user->save())
                {
                    $this->template->title = "User created.";
                    $this->template->content = "<p>The user <strong>" . $username .  "</strong> was created with password <strong>" . $password . "</strong></p>";
                }
            } else {
                $this->template->title = "User creation failed.";
                $this->template->content = "<p>You're not an admin</p>";
            }
        }
        
        public function change_password($username, $password) {
            if (Auth::instance()->logged_in("admin")) {
                $user = ORM::factory("user");
                $user->username = $username;
                $user->find();
                $user->password = $password;
                
                if ($user->save())
                {
                    $this->template->title = "Password changed.";
                    $this->template->content = "<p>The user <strong>" . $username .  "</strong> has now password <strong>" . $password . "</strong></p>";
                }
            } else {
                $this->template->title = "Changing password has failed.";
                $this->template->content = "<p>You're not an admin</p>";
            }
        }
        
        public function create_admin($username, $password) {
            if (Auth::instance()->logged_in("admin")) {
                $user = ORM::factory("user");
                $user->username = $username;
                $user->email = "random" . rand(10,99999) . "@random.com";
                $user->password = $password;
                
                //ORM::factory('role', 'login') returns orm object and get's value from colum with name=login
                //$user->add creates a relation between $user and role orm model returned by ORM::factory('role', 'login')
                if ($user->add(ORM::factory('role', 'login')) AND $user->add(ORM::factory('role', 'admin')) AND $user->save())
                {
                    $this->template->title = "Admin created.";
                    $this->template->content = "<p>The admin <strong>" . $username .  "</strong> was created with password <strong>" . $password . "</strong></p>";
                }
            } else {
                $this->template->title = "Admin creation failed.";
                $this->template->content = "<p>You're not an admin</p>";
            }
        }
        
        public function cache_clear_cache() {
            Wi3::cache_field_delete_all(); //will also delete all pagecaches
        }
        
        
        //this functions makes sure that any views are loaded in either the Wi3::$workplace or the Wi3::$template namespace
        //depending on whether a site is rendered ("edit_page" action) or that just a workplace page is displayed
        //so the $this in these views refers to Wi3::$workplace
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            if (Wi3::$routing->action == "edit_page") {
                //we want the pagetemplate (and other templates) to be available through the Wi3_template namespace
                return Wi3::$template->_kohana_load_view($kohana_view_filename, $kohana_input_data);
            } else {
                //we want the pagetemplate (and other templates) to be available through the Wi3_workplace namespace
                return Wi3::$workplace->_kohana_load_view($kohana_view_filename, $kohana_input_data);
            }
        }
        
    }

?>