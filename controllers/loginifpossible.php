<?php

    class LoginIfPossible_Controller extends Template_Controller {
     
        //make sure every user that uses this Controller is logged in
        public function __construct() {
            parent::__construct();
            
            $this->session = Session::instance();
            $this->auth = Auth::instance();
            
            if ($this->auth->logged_in()) {
                //logged in
                //that's all right
                Wi3::$user = $this->auth->get_user();
                $this->session->set("userid", Wi3::$user->id);
                //set cache-addendum
                Wi3::$cache->page_addendums["wi3_login_userid"] = Wi3::$user->id;
            } else {
                //not logged in
                
                //try to auto-login
                if ($this->auth->auto_login() == TRUE) {
                    //all right, logged in
                    Wi3::$user = $this->auth->get_user();
                    $this->session->set("userid", Wi3::$user->id);
                    //set cache-addendum
                    Wi3::$cache->page_addendums["wi3_login_userid"] = Wi3::$user->id;                    
                } else {
                    $this->session->delete("userid");
                    //well, no login possible. That's not a problem, just continue
                }
            }
        }
        
    }
    
?>