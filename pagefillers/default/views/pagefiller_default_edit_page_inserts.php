<?php
    //page ID and edit mode are inserted by the Wi3_Workplace library in a JSON Javascript object (called 'workplace')
    //echoed in <script> tags and thus available to all javascript functions

    //Jquery UI CSS and different scripts are inserted in the 'wi3/workplace' view
    //so we don't need to include them here again.
    //What we do need however, is the workplace_insite.js
    Wi3::$pagefiller->javascript("pagefiller_default_edit_site.js");
    
    /*
    //echo scripts and JQuery UI CSS
    Wi3::$workplace->css("jquery_ui_flick/jquery-ui-1.7.2.custom.css");
    
    Wi3::$workplace->javascript(array(
        //'http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js',
        'jquery/jquery-1.3.2.min.js', //jquery base
        'jquery/jquery-ui-1.7.2.custom.min.js', //jquery base
        'jquery/jquery.simple.tree.js', //for tree displaying plus drag&drop        
        'kohana.js', //clientside client<>server communcation module
        'workplace.js', //creates the hovers, dragdropping etc in the 'menu' page and contains the Iframe-functions
    )); 
*/
?>