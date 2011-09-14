<?php

    //
    class Tinymce_Controller extends Login_Controller {
        
        public function __construct()
        {
            parent::__construct();
            //$this->db = Database::instance();
        }
        
        function index() {
            //nothing
        }
        
        public function images() {
            
            $this->template = View::factory("tinymce/images");
            $files = Wi3::$files->find(Array("whereExt" => Array("png", "jpg", "gif", "jpeg", "bmp")));
            $images = $files;
            $this->template->images = $images;
        }
        
        public function insertlink() {
            
            //run this event, so that Wi3::urlof will load $urlof->site() so that URLs will appear properly
            Event::run("wi3.siteandpageloaded");
            
            $this->template = View::factory("tinymce/insertlink");
            $files = Wi3::$files->find();
            $this->template->files = $files;
        }
        
        
    }
    
    
?>
