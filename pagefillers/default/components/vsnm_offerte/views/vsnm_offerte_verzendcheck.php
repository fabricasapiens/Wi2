<?php
if(isset($_POST['verzenden']))
{
    ob_start();
    ?>

    <html>
    <head>
    <link rel="stylesheet" href="stylesheet.css" type="text/css">
    </head>

    <body>

    <table width=440 border=0 cellspacing=0 cellpadding=0>
    <tr><td colspan=2><i>Persoonlijke gegevens</i></td></tr>
    <tr><td width=200>Naam:</td><td><?php echo $_POST['naam']; ?></td></tr>
    <tr><td>Geslacht:</td><td><?php echo (isset($_POST["geslacht"]) ? $_POST['geslacht'] : "onbekend"); ?></td></tr>
    <tr><td>Telefoonnummer:</td><td><?php echo $_POST['telefoon']; ?></td></tr>
    <tr><td>Telefoon overdag:</td><td><?php echo $_POST['telefoon_overdag']; ?></td></tr>
    <tr><td>E-mail:</td><td><?php echo $_POST['email']; ?></td></tr>
    <tr><td colspan=2 height=20></td></tr>

    <tr><td valign=top>Informatie aanvragen over:</td><td><?php echo $_POST['opmerkingen']; ?></td></tr>
    </table>

    </body>

    </html>

    <?php
    $inhoud=ob_get_contents(); 
    ob_end_clean();
            
    $ontvanger="VSN Midden <info@vsnm.nl>";
    $onderwerp="Ingevuld contactformulier van VSNM.nl";
    $headers="MIME-Version: 1.0\n";
    $headers.="Content-type: text/html; charset=iso-8859-1\n";
    $headers.="From: contactformulier <no-reply@vsnm.nl>\n";
    $headers .= "Reply-To: " . strip_tags($_POST['naam']) . " <" . strip_tags($_POST["email"]) . ">\n";

}

if(isset($_POST['verzenden']))
{
    
    //mail ook gewoon als tekst opslaan...
    try {
        $log = "";
        if (file_exists("/var/www/vhosts/selfstoragehouten.nl/httpdocs/mails/mails.html")) {
            $log = file_get_contents("/var/www/vhosts/selfstoragehouten.nl/httpdocs/mails/mails.html");
        }
        $log = "<p><strong>" . $onderwerp . "</strong></p><p>" . $inhoud . "<p>" . $log;
        file_put_contents("/var/www/vhosts/selfstoragehouten.nl/httpdocs/mails/mails.html", $log);
    } catch (Exception $e) {
        
    }
    
	if(mail($ontvanger, $onderwerp, $inhoud, $headers)) echo "<b>Hartelijk dank voor u aanvraag. Wij nemen z.s.m. contact met u op.</b><p>";
	else echo "<b>Helaas is er iets mis gegaan. Probeert u het alstublieft nog eens. Mocht het dan nog niet werken, aarzelt u dan vooral niet om ons direct te bellen (030-2804577).</b><p>";
}

?>