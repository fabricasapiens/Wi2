<?php

    echo "<h2>Bericht na aanmelding</h2>";
    echo "<textarea id='component_loginform_change_thankyoumessage' style='width: 100%; height: 120px;'>";
    echo $thankyoumessage;
    echo "</textarea><br /><button onClick='component_loginform_change_thankyoumessage()'>wijzig bericht</button>";
    
    echo "<h2>Bericht als aanmelding niet voltooid kon worden en een fout genereerde</h2>";
    echo "<textarea id='component_loginform_change_errormessage' style='width: 100%; height: 120px;'>";
    echo $errormessage;
    echo "</textarea><br /><button onClick='component_loginform_change_errormessage()'>wijzig bericht</button>";
    
?>  