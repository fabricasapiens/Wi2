<?php defined('SYSPATH') or die('No direct script access.');
    
    class File_Model extends Base_mptt_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        //this is 'extended' from the Base_mptt_Model, so thinks like 'scope' 'leftnr and 'rightnr' are automatically included
       public $_columns = Array(
            "id" => Array("integer"),
            "title" => Array("string"),
            "type" => Array("string", "default" => "file"), //folder or file
            "filename" => Array("string"),
            "description" => Array("string"),
            
            "user_id" => Array("integer"), //who is owner of this file (usually the creator/uploader)
            "viewright" => Array("string"), //what right does a user/group need to view
            "editright" => Array("string"), //what right does a user/group need to edit this file
            "adminright" => Array("string"), //right does a user/group need to admin this file (includes deleting it and setting the rights for this file)
            
            "deleted" => Array("boolean"), //do not show in navigation etc
            "visible" => Array("boolean"), //if set to false, it will not show up in listings (file listing etc), however will still be accesible (unless active is set to false)
            "active" => Array("boolean"), //to temporarely disable this page
            
            "site_id" => Array("integer"), //belongs to a certain site
            
            "created" => Array("timestamp"), //when this field was created
            "lastupdated" => Array("timestamp"), //when this field was updated (use at will)
        );
        
        public $belongs_to = array('site', 'user');
        
        public $_db = 'sitespecificconfig';   // this will make the ORM model try to use the Database instance 'sitespecificconfig'
                                                                // this config is loaded in the view-controller, where the config is loaded from sites/sitename/config/database.php
        
        //use ORM for rest of functions
        
        

    }
    
?>