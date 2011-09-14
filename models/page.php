<?php defined('SYSPATH') or die('No direct script access.');
    
    class Page_Model extends Base_mptt_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        //this is 'extended' from the Base_mptt_Model, so thinks like 'scope' 'leftnr and 'rightnr' are automatically included
        public $_columns = Array(
            "id" => Array("integer"),
            "title" => Array("string"),
            "url" => Array("string"), //what URL-part is used to identify this page (eg domain.nl/url)
            "description" => Array("string"),
            
            "user_id" => Array("integer"), //who is owner of this page (default is the creator of the page)
            "moveright" => Array("string"), //what right/role does a user/group need to move this page around
            "viewright" => Array("string"), //what right does a user/group need to view
            "editright" => Array("string"), //what right does a user/group need to edit this page
            "adminright" => Array("string"), //right does a user/group need to delete the page or edit one of the other rights. Default is 'siteadmin' 
            
            "deleted" => Array("boolean"), //flag to make it deleted
            "visible" => Array("boolean"), //if set to false, it will not show up in listings (navigation etc), however will still be accesible (unless active is set to false)
            "active" => Array("boolean"), //to temporarely disable this page
            
            "created" => Array("timestamp"), //when this field was created
            "lastupdated" => Array("timestamp"), //when this field was updated (use at will)
            
            "site_id" => Array("integer"), //belongs to a certain site
            
            "page_type" => Array("string"), //what type of page this is. Neccesary to let the pagefiller know what page it should deal with
            "page_filler" => Array("string"), //the filler that fills this page, maybe loads the page_template and renders for example all the fields or loads an external page
            "page_template" => Array("string"), //what template should be used to render this page
            "page_templatetype" => Array("string"), //Either 'wi3' or 'user', indicating where the page_template is stored
            "page_redirect_type" => Array("string"), //Whether there is a redirect. Possible values include 'none', 'wi3', 'external'
            "page_redirect_wi3" => Array("string"), //Pageid where page is redirected to. 
            "page_redirect_external" => Array("string") //External URL where page is to be redirected to
        );
        
        public $belongs_to = array('site', 'user');
        public $has_many = array('fields');
        public $has_and_belongs_to_many = array("tags");

	    public $primary_key = "id";
        public $primary_val = "id";
        
        public $_db = 'sitespecificconfig';   // this will make the ORM model try to use the Database instance 'sitespecificconfig'
                                                                // this config is loaded in the view-controller, where the config is loaded from sites/sitename/config/database.php
        
        //use ORM for rest of functions

        public function __SET($key, $val) {
            if ($key == "title") {
                //url ook meteen aanpassen!
                //zoeken of deze url voor deze pagina al bestaat...
                $url = url::title($val);
                $obj = ORM::factory("page")->where("url", $url)->where("site_id", $this->site_id)->where("id !=", $this->id)->find();
                $counter = 1;
                while($obj->loaded === true) {
                    //if url exists already in another object (and so we are not that object itself) , make a new url and check again
                    $url = url::title($val) . "-" . $counter;
                    $counter++;
                    $obj = ORM::factory("page")->where("url", $url)->where("site_id", $this->site_id)->where("id !=", $this->id)->find();
                }
                $this->url = $url;
            }
            return parent::__SET($key,$val);
        }
        
        public function __GET($key) {
            try {
                $result = parent::__GET($key);
            } catch(Exception $e) {
                //the field does not exist in this Page object
                //pass it along to the Pagefiller, so that he might handle this...
                $pagefillername = $this->page_filler;
                $result = Pagefiller_default::page_properties($this,$key);
            }
            return $result;
        }

        /**
         * Allows a model to be loaded by id or title
         */
        public function unique_key($id)
        {
            if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
            {
                return 'title';
            }

            return parent::unique_key($id);
        }

    }
    
?>
