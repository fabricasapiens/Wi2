<?php defined('SYSPATH') or die('No direct script access.');
    
    class Site_Model extends Base_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        public static $_columns = Array(
            "id" => Array("integer"),
            "title" => Array("string"),
            "url" => Array("string"), //preferred url, as in http://../users/url 
            
            "modules" => Array("string"), //serialized array of the modules a site has enabled (language-settings, move pages around, create new pages, etc)
            "components" => Array("string"), //serialized array of the components site hasenabled (text, events, contact form etc)
            "page_templates" => Array("string"), //serialized array of the page templates a site has enabled (ie, some standards templates, or its own page templates)
            
            "editmode" => Array("string"), //if we want to use the simple or advanced edit mode
            "default_page_template" => Array("string"), //newly added pages will get this page_template
            "default_page_templatetype" => Array("string"), //newly added pages will get this page_templatetype ('wi3' or 'user')
            
            "created" => Array("timestamp"), //when this site was created
            "lastupdated" => Array("timestamp"), //when this site was last updated (use at will)
        );
        
        public $has_many = array('pages', 'files');
        public $has_and_belongs_to_many = array('tags', 'users');

	    public $primary_key = "id";
        public $primary_val = "id";
        
        //use ORM for rest of functions
        
        //enabled modules, components en page_templates staan in een array, maar zijn geserialized opgeslagen...
        public function __GET($key) {
            if ($key == "modules" OR $key == "components" OR $key == "page_templates") {
                if (isset($this->object[$key])) {
                    $get = unserialize($this->object[$key]);
                    if (is_array($get)) { return $get; } else { return Array(); }
                } else {
                    return Array();
                }
            }
            return parent::__GET($key);
        }
        
        //enabled modules, components en page_templates staan in een array, maar zijn geserialized opgeslagen...
        public function __SET($key, $val) {
            if ($key == "modules" OR $key == "components" OR $key == "page_templates") {
                if (is_array($val)) {
                    return ($this->object[$key] = serialize($val));
                } else {
                    //misschien is het al geserializede tekst
                    $arr = @unserialize($val);
                    if ($arr === false) { 
                        return false;
                    } else {
                        $this->object[$key] = $val;
                    }
                }
            }
            return parent::__SET($key,$val);
        }
        
        /**
         * Allows a model to be loaded by id or url
         */
        public function unique_key($id)
        {
            if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id))
            {
                return 'url';
            }

            return parent::unique_key($id);
        }

    }
    
?>
