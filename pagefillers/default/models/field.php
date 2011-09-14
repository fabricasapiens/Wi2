<?php defined('SYSPATH') or die('No direct script access.');
    
    class Field_Model extends Base_mptt_Model {
        
        //defines how the underlying DB-table would look like. Handy for creating Tables or generating forms
        //this is 'extended' from the Base_mptt_Model, so thinks like 'scope' 'leftnr and 'rightnr' are automatically included
        public $_columns = Array(
            "id" => Array("integer"),
            "type" => Array("string"), //type of field
            "created" => Array("timestamp"), //when this field was created
            "lastupdated" => Array("timestamp"), //when this field was updated (use at will)
            "page_id" => Array("integer"), //belongs to a certain page
            "site_id" => Array("integer") //and belongs to a certain site
        );
        
        public $belongs_to = array('page');
        
        //functions to generate the ID and class that is to be used in <div>s where the content of the field is dumped in
        public function htmlid() {
            return "wi3_field_" . $this->id;
        }
        
        public function htmlclass() {
            return "wi3_field";
        }
        
        public function event($eventname, $data) {
            if ($eventname == "field_added") {
                //create component for this field, and pass it the information
                $component = Pagefiller_default::factory("component", $this->type);
                if (method_exists($component, "event")) {
                    $component->event("field_added", array("field" => $this, "config" => $data));
                }
            }
        }
        
        //functions to render the content of this field
        public function render() { return $this->get_content(); } //alias
        public function get_content() {
            //load component that belongs to this field and ask it to render this field
            ob_start();
            $content = Pagefiller_default::factory("component", $this->type)->render_field($this);
            $content = ob_get_contents() . $content; //prepend any echoed content
            ob_end_clean();
            return $content;
        }
        
        public function renderwithindiv() { 
            return "<div class='" . $this->htmlclass() . "' id='" . $this->htmlid . "'>" . $this->render() . "</div>";
        }

        //rest:
        //set and get functions can be found in the Base Model
        //ORM data saving can be found in the Data Model

    }
    
    
?>
