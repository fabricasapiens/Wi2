<?php defined('SYSPATH') or die('No direct script access.');
    
    class Role_Model extends Auth_Role_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        public $_columns = Array(
            "id" => Array("integer"),
            "name" => Array("string", "unique" => "yes"),
            "description" => Array("string"),
        );
        
        public $has_and_belongs_to_many = Array("users");
        
        public $_db = 'sitespecificconfig';   // this will make the ORM model try to use the Database instance 'sitespecificconfig'
                                                                // this config is loaded in the view-controller, where the config is loaded from sites/sitename/config/database.php
        
        //use ORM for rest of functions

    }
    
?>