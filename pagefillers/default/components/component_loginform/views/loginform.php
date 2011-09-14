<?php

    if (isset($thankyoumessage)) {
        echo "<p>" . $thankyoumessage . "</p>";
    } else {
        if (isset($errormessage)) {
            echo "<p>" . $errormessage . "</p>";
        }
        echo $form->get();
    }
            

?>