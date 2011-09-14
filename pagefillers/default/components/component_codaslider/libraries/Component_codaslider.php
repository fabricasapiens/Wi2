<?php

    Class Component_codaslider extends Pagefiller_default_component {
        
        public $wi3_dependencies_plugins = array("Plugin_jquery_1_3_2_codaslider", "Plugin_jquery_1_3_2_wi3", "Plugin_clientjavascriptvars");
        public $wi3_cacheable = false;
        public $wi3_cachetitle = "";
        
        public function __construct() {
            parent::__construct();  
            Wi3::$template->wi3_cacheable = false;
        }
        
        public function render_field($field) {
            
            //first, render the slider
            echo '
            <div class="coda-slider-wrapper"> 
                <div class="coda-slider preload" id="coda-slider-' . $field->id . '"> 
                    <div class="panel" > 
                        <div class="panel-wrapper"> 
                            <h2 class="title">Panel 1</h2> 
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas metus nulla, commodo a sodales sed, dignissim pretium nunc. Nam et lacus neque. Sed volutpat ante id mauris laoreet vestibulum. Nam blandit felis non neque cursus aliquet. Morbi vel enim dignissim massa dignissim commodo vitae quis tellus. Nunc non mollis nulla. Sed consectetur elit id mi consectetur bibendum. Ut enim massa, sodales tempor convallis et, iaculis ac massa. Etiam suscipit nisl eget lorem pellentesque quis iaculis mi mattis. Aliquam sit amet purus lectus. Maecenas tempor ornare sollicitudin.</p> 
                        </div> 
                    </div> 
                    <div class="panel"> 
                        <div class="panel-wrapper"> 
                            <h2 class="title">Panel 2</h2> 
                            <p>Proin nec turpis eget dolor dictum lacinia. Nullam nunc magna, tincidunt eu porta in, faucibus sed magna. Suspendisse laoreet ornare ullamcorper. Nulla in tortor nibh. Pellentesque sed est vitae odio vestibulum aliquet in nec leo.</p> 
                        </div> 
                    </div> 
                    <div class="panel"> 
                        <div class="panel-wrapper"> 
                            <h2 class="title">Panel 3</h2> 
                            <p>Cras luctus fringilla odio vel hendrerit. Cras pulvinar auctor sollicitudin. Sed lacus quam, sodales sit amet feugiat sit amet, viverra nec augue. Sed enim ipsum, malesuada quis blandit vel, posuere eget erat. Sed a arcu justo. Integer ultricies, nunc at lobortis facilisis, ligula lacus vestibulum quam, id tincidunt sapien arcu in velit. Vestibulum consequat augue et turpis condimentum mollis sed vitae metus. Morbi leo libero, tincidunt lobortis fermentum eget, rhoncus vel sem. Morbi varius viverra velit vel tempus. Morbi enim turpis, facilisis vel volutpat at, condimentum quis erat. Morbi auctor rutrum libero sed placerat. Etiam ipsum velit, eleifend in vehicula eu, tristique a ipsum. Donec vitae quam vel diam iaculis bibendum eget ut diam. Fusce quis interdum diam. Ut urna justo, dapibus a tempus sit amet, bibendum at lectus. Sed venenatis molestie commodo.</p> 
                        </div> 
                    </div> 
                    <div class="panel"> 
                        <div class="panel-wrapper"> 
                            <h2 class="title">Panel 4</h2> 
                            <p>Nulla ultricies ornare erat, a rutrum lacus varius nec. Pellentesque vehicula lobortis dignissim. Ut scelerisque auctor eros sed porttitor. Nullam pulvinar ultrices malesuada. Quisque lobortis bibendum nisi et condimentum. Mauris quis erat vel dui lobortis dignissim.</p> 
                        </div> 
                    </div> 
                </div><!-- .coda-slider --> 
            </div>';
    
            //now enable it
            echo "
            <script>
                $().ready(function() {
                    //make the elements fill the screen width
                    $('#coda-slider-" . $field->id . "').css('width', $('#coda-slider-" . $field->id . "').parent().width() + 'px');
                    $('#coda-slider-" . $field->id . " .panel').css('width', $('#coda-slider-" . $field->id . "').outerWidth() + 'px');
                    //now enable the slider on it
                    $('#coda-slider-" . $field->id . "').codaSlider( { autoHeight: false, dynamicArrows: false, slideEaseDuration: 200, slideEaseFunction: 'easeInOutExpo' } );
                });
            </script>
            ";
            
        }
        
        public function start_edit_field($field) {
            
        }
        
    }

?>