<?php

    class Login_Controller extends Template_Controller {
     
        public $template = "wi3/login";
     
        //make sure every user that uses this Controller is logged in
        public function __construct() {
            parent::__construct();
            
            $this->session = Session::instance();
            $this->auth = Auth::instance();
            
            if ($this->auth->logged_in() OR $this->auth->auto_login() == TRUE) {
                //logged in
                //that's all right
                Wi3::$user = $this->auth->get_user();
                Wi3::$site = Wi3::$user->sites[0];
                //set cache-addendum
                Wi3::$cache->page_addendums["wi3_login_userid"] = Wi3::$user->id;
                //opnieuw URLs en PATHs uitzoeken
                Wi3::$urlof = new Wi3_urlof();
                Wi3::$pathof = new Wi3_pathof();
                $this->session->set("userid", Wi3::$user->id);
                $this->session->set("sitefolder", Wi3::$pathof->site); //set this for use by for example the Tinymce advimage plugin
            } else {
                if (url::current() != "login/login") {
                    //set this page as 'last requested' and redirect to login-page
                    $this->session->set("requested_url","/".url::current());
                    url::redirect("login/login"); //redirect to login-page
                } else {
                    //we're already on the login-page (maybe even with a POST), so no redirect necessary!
                }
            }
            
            //create template 
            //edit: use Template_Controller instead
            //$this->template = new View($this->template);
            
            //add a Profiler for extra debug information if you want
            //$profiler = new Profiler();
            
        }
        
        public function index() {
            url::redirect("login/login");
        }
        
        public function login() {
            $this->template->title = "Log in op Wi3";
            $this->template->navigationright = View::factory("wi3/partial/workplacenavigationright");
            //try to login user if $_POST is supplied
            $form = $_POST;
			if($form){
                if ($this->auth->login($form['username'], $form['password'], TRUE)) //set remember option to TRUE
				{
					// Login successful, redirect
                    if ($this->session->get("requested_url") != null AND $this->session->get("requested_url") != "login/login") {
                        url::redirect($this->session->get("requested_url")); //return to page where login was called
                    } else {
                        url::redirect(""); //redirect to home-page
                    }
				}
				else
				{
					$this->template->content = '<p>Login mislukt.</p>';
                    $this->template->content .= View::factory("wi3/partial/loginform")->render(FALSE);
                    return;
				}
			}
            
            if ($this->auth->logged_in()) {
                url::redirect(""); //redirect to homepage
                //$this->template->content = 'You are logged in as ' . $this->auth->get_user()->username;
                return;
            }
            
            $this->template->content = View::factory("wi3/partial/loginform")->render(FALSE);
        }
        
        public function logout() {
            $this->auth->logout(TRUE);
            url::redirect("/");
        }
        
        //this functions makes sure that any views are loaded in the Wi3::$workplace namespace
        //so the $this in these views refers to Wi3::$workplace
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            //we want the pagetemplate (and other templates) to be available through the Wi3_workplace namespace
            return Wi3::$workplace->_kohana_load_view($kohana_view_filename, $kohana_input_data);
        }
        
    }
    
?>