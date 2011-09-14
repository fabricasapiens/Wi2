<?php defined('SYSPATH') or die('No direct script access.');
    
    class User_Model extends Auth_User_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        public $_columns = Array(
            "id" => Array("integer"),
            "email" => Array("string"),
            "username" => Array("string"),
            "password" => Array("string"),
            "logins" => Array("integer"),
            "last_login" => Array("integer"),
            //voor setup
            "role_id" => Array("integer", "foreign" => "roles"),
            "site_id" => Array("integer", "foreign" => "sites"),
        );
        public $has_and_belongs_to_many = Array("roles", "sites");
        public $has_many = Array("pages", "files");
        
        public $_db = 'sitespecificconfig';   // this will make the ORM model try to use the Database instance 'sitespecificconfig'
                                                                // this config is loaded in the view-controller, where the config is loaded from sites/sitename/config/database.php
        
        //use ORM for rest of functions

	    public $primary_key = "id";
        public $primary_val = "id";

    }
    
?>
