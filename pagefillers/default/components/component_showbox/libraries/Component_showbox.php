<?php

    Class Component_showbox extends Pagefiller_default_component {
        
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_3_2_wi3", "Plugin_clientjavascriptvars");
        public $wi3_cacheable = false;
        public $wi3_cachetitle = "";
        
        public function __construct() {
            parent::__construct();  
            Wi3::$template->wi3_cacheable = false;
        }
        
        public function render_field($field) {
            //include javascript and css
            $this->javascript("jquery.timers-1.1.2.js");
            $this->javascript("jquery.galleryview-2.0.pack.js");
            $this->css("galleryview.css");
            //include the javascript file that will execute the function to build the gallery
            $this->javascript("galleryview.js");
            //display the gallery HTML
            ob_start();
            echo "<div id='photostest' class='galleryview'>";
                foreach(Wi3::$files->find() as $file) {
                    echo "<div class='panel'>
                        <img src='" . Wi3::$urlof->image($file->url, 600) . "'/>
                        <div class='panel-overlay'>
                            <h2>" . $file->filename . "</h2>
                            en nog allerlei tekst
                        </div>
                        <div class='overlay-background'></div>
                    </div>";
                }
                echo "<ul class='' style='background: #000; height: 100px;'>";
                foreach(Wi3::$files->find() as $file) {
                    echo "<li class='frame'><img src='" . Wi3::$urlof->image($file->url,600) . "'/><div class='caption'>caption text</div></li>";
                }
                echo "</ul>";
            echo "</div>";
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
            
        }
        
    }

?>