<?php

    class Component_loginform extends Pagefiller_default_component {
        
        public $wi3_cacheable = false; //this field is cacheable by default. Can be set to false in constructor (Cache will always instantiate, then check for this var)
        public $wi3_dependencies_plugins = array(); //plugins that need to be loaded before this component can do its work
        
        public function __construct() {
            if (Wi3::$editmode == true) {
                //if we are in editmode, we will need JQuery to be enabled, as well as the wi3<>kohana communication module
                $this->wi3_dependencies_plugins = array( "Plugin_jquery_1_4_2_core", "Plugin_jquery_1_4_2_wi3", "Plugin_jquery_1_4_2_ui", "Plugin_tinymce");
                //load javascript that is needed for editing
                $this->javascript("component_loginform_edit.js");
            }
            //load css
            $this->css("component_loginform.css");
            parent::__construct(); //here, the required plugins will be loaded. We could alternatively also just do Wi3::$plugins->load("Plugin_jquery_1_3_2");
        }
        
        //render a certain field
        //$field is always a valid Field Object
        public function render_field($field) {
            
            //check whether the user wants to log out
            if (isset($_POST) AND isset($_POST["logout"])) {
                $this->auth = Auth::instance();
                $this->auth->logout(TRUE);
                Wi3::$user = null;
                unset($_POST["logout"]);
            }
            
            //load view
            $view = $this->view("loginform");
            
            //now, check whether the user is logged in
            if (isset(Wi3::$user)) {
                $view->thankyoumessage = "U bent momenteel ingelogd als " . Wi3::$user->username . " <form style='display: inline;' method='POST'><input style='display: none;' type='hidden' name='logout'/><button style='display: inline;'>uitloggen</button></form>";
            } else {
                
                //load form and check for validation
                //for some reason, the csfr plugin does not work when we have just logged out of the site
                //remove it for now
                //TODO: ADD CSFR PLUGIN
                $f = Formo::factory()->plugin('auto_i18n');
                //$f = Formo::factory()->plugin('auto_i18n')->plugin('csrf');
                //make the form look pretty in a table
                // or if you need it set globally but only for this one form
                
                $f->add("text", "username", array("label" => "naam"));
                $f->add("password", "password", array("label" => "wachtwoord"));
                $f->add("submit", "_submit", "inloggen");
                
                //check if form is ok
                if ($f->validate()) {
                    //try to login
                    $values = $f->get_values();
                    $this->auth = Auth::instance();
                    $this->session = Session::instance();
                    if ($this->auth->login($values['username'], $values['password'], TRUE)) { //set remember option to TRUE
                        //that's all right
                        Wi3::$user = $this->auth->get_user();
                        $this->session->set("userid", Wi3::$user->id);
                        //set cache-addendum
                        Wi3::$cache->page_addendums["wi3_login_userid"] = Wi3::$user->id;
                        //show thank you message
                        $thankyoumessage = $field->get("thankyoumessage");
                        $view->thankyoumessage = (!empty($thankyoumessage) ? $thankyoumessage  : "U bent ingelogd!");
                    } else {
                        //login did not work
                        //show form and error message
                        $errormessage = $field->get("errormessage");
                        $view->thankyoumessage = (!empty($errormessage) ? $errormessage  : "U kon <strong>niet ingelogd</strong> worden met de verstuurde gegevens!");
                        $view->form = $f;
                    }
                    
                } else {
                    //display form (along with errors if there were any)
                    $view->form = $f;
                }
            }
            
            return $view->render(TRUE);
        }
        
        //is called when a user starts to edit a field
        function start_edit_field($field) {
            
            //load thankyoumessage and errormessage
            $thankyoumessage = $field->get("thankyoumessage");
            $errormessage = $field->get("errormessage");
            
            //load view and pass form and thankyoumessage to it, as well as current results
            $view = $this->view("loginform_edit");
            $view->thankyoumessage = $thankyoumessage;
            $view->errormessage = $errormessage;
            $view->field = $field;
            
            echo str_replace("\\n", "", json_encode(Array(
                "dom" => Array(
                    "fill" => Array("#wi3_editdiv_content" => $view->render())
                ),
                "scriptsafter" => Array("wi3_editdiv_show();")
            )));
        }
        
    }
    
?>
