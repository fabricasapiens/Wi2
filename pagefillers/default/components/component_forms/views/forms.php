<?php

    if (isset($thankyoumessage)) {
        echo "<p>" . $thankyoumessage . "</p>";
    } else {
        echo $form->get();
    }
            

?>