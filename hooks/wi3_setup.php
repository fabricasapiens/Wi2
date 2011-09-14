<?php

    //add Wi3 setup AFTER controller file is loaded (so that Reflection loading the controller class will not include the controller file before Kohana does)
    //but BEFORE the controller object is created
    Event::add('system.pre_controller', array('Wi3', 'setup'));

?>