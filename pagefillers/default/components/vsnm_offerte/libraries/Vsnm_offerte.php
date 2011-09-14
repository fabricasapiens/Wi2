<?php
    
    class Vsnm_offerte extends Pagefiller_default_component {
        
        public function render_field($field) {
            $verzendcheck = $this->view("vsnm_offerte_verzendcheck")->render(FALSE);
            return $verzendcheck . $this->view("vsnm_offerte")->render(FALSE);
        }
        
    }

?>