<h1 id='controlpanel_user'>Gebruiker</h1>
<table class='padded'>
    <tr><td>ingelogd als</td><td><?php echo Wi3::$user->username; ?></td></tr>
    <tr class='zebrastreep'><td>ingelogd sinds</td><td><?php echo date("d-m-Y H:i:s", Wi3::$user->last_login); ?></td></tr>
    <tr><td>aantal keer ingelogd</td><td><?php echo Wi3::$user->logins; ?></td></tr>
</table>

<?php

    //statistieken over aantal pagina's en aantal bezoeken per pagina ed
    
    //geïnstalleerde modules (waarbij je nieuwe
    
    //verzin iets ;-)

?>