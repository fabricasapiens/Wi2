<?php defined('SYSPATH') or die('No direct script access.');
    
    class SiteField_Model extends Base_Model {
        
        /*
        A static field is defined as a field that is static and is called directly from within a site template
        Therefore, it is NOT part of a page, but rather part of a site
        As such, it can be displayed/edited from a variety of pages
        A static field is referred to by a title, the id is hidden to the template

        A template could render a static field like this;

        $staticfield = Wi3::factory("static_field", "somename");
        echo Wi3::factory("component", "somename")->render_field($staticfield);

        if it wants to echo a wi3_staticfield around it (ie, in editmode)
        you should wrap it yourself, ie
        echo "<div class='wi3_staticfield' id='wi3_staticfield_" . $staticfield->id . "

        */
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        //this is 'extended' from the Base_mptt_Model, so thinks like 'scope' 'leftnr and 'rightnr' are automatically included
        public $_columns = Array(
            "id" => Array("integer"),
            "title" => Array("string"),
            "type" => Array("string"), //type of field
            "created" => Array("timestamp"), //when this field was created
            "lastupdated" => Array("timestamp"), //when this field was updated (use at will)
            "site_id" => Array("integer") //belongs to a certain site, NOT a page
        );
        
        public $belongs_to = array('site');
        
        //functions to generate the ID and class that is to be used in <div>s where the content of the field is dumped in
        public function htmlid() {
            return "wi3_siteField_" . $this->id;
        }
        
        public function htmlclass() {
            return "wi3_siteField";
        }
        
        //functions to render the content of this field
        public function render() { return $this->get_content(); } //alias
        public function get_content() {
            //load component that belongs to this field and ask it to render this field
            return Pagefiller_default::factory("component", $this->type)->render_field($this);
        }
        
        public function renderwithindiv() { 
            return "<div class='" . $this->htmlclass() . "' id='" . $this->htmlid() . "'>" . $this->render() . "</div>";
        }

    }
    
    
?>
