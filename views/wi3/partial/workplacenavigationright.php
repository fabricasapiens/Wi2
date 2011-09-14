<?php

    //the wi3 ajax request indicator
    echo "<div id='wi3_ajax_menu'>
        <div id='wi3_ajax_indicator'>0</div>
        <div id='wi3_notification_bottom'>hallo1</div>
        <div id='wi3_notification_top'>hallo2</div>
    </div>";

    if (isset(Wi3::$user)) {
        echo "Ingelogd als <strong>" . Wi3::$user->username . "</strong>. [" . html::anchor("login/logout", "Log uit") . "]";
    } else {
        echo "Niet ingelogd.";
    }

?>