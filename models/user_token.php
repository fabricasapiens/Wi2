<?php defined('SYSPATH') or die('No direct script access.');
    
    class User_Token_Model extends Auth_User_Token_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        public $_columns = Array(
            "id" => Array("integer"),
            "user_id" => Array("integer"),
            "user_agent" => Array("string"),
            "token" => Array("string"),
            "created" => Array("integer"),
            "expires" => Array("integer")
        );
        protected $has_and_belongs_to_many = array("pages");
        
        public $_db = 'sitespecificconfig';   // this will make the ORM model try to use the Database instance 'sitespecificconfig'
                                                                // this config is loaded in the view-controller, where the config is loaded from sites/sitename/config/database.php
        
        //use ORM for rest of functions

    }
    
?>