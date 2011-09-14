<?php

    //----------------------------------------
    // this class is the class in which the functions and vars are positioned that the workplace uses
    // these functions are available via the $this variable in all pagetemplates of the workplace
    // 
    // the functions are also available via Wi3::$workplace
    //---------------------------------------
    class Wi3_workplace {
            
        //-------------------------------------------------------------
        // these functions are used to include static content from the Wi3/static folder
        //-------------------------------------------------------------
        public function css($file, $category = "wi3") {
            if (is_array($file)) {
                foreach($file as $f) { 
                    Css::add(Wi3::$urlof->wi3 . "static/css/" . $f, $category);
                }
            } else {
                Css::add(Wi3::$urlof->wi3 . "static/css/" . $file, $category);
            }
        }
        
        public function javascript($file, $category = "wi3") {
           if (is_array($file)) {
                foreach($file as $f) { 
                   Javascript::add(Wi3::$urlof->wi3. "static/javascript/" . $f, $category);
                }
            } else {
                Javascript::add(Wi3::$urlof->wi3 . "static/javascript/" . $file, $category);
            }
        }    
        
        //-------------------------------------------------------------
        //function that is used to include the template and make it available through the namespace of this class
        //copied from the core Controller in Kohana core folder
        //-------------------------------------------------------------
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            if ($kohana_view_filename == '')
                return;

            // Buffering on
            ob_start();

            // Import the view variables to local namespace
            extract($kohana_input_data, EXTR_SKIP);

            // Views are straight HTML pages with embedded PHP, so importing them
            // this way insures that $this can be accessed as if the user was in
            // the controller, which gives the easiest access to libraries in views
            try
            {
                include $kohana_view_filename;
            }
            catch (Exception $e)
            {
                ob_end_clean();
                throw $e;
            }

            // Fetch the output and close the buffer
            return ob_get_clean();
        }
        
    }

?>