<h1 id="controlpanel_site_common">Site algemeen</h1>
<?php
    if (isset($changetitle)) {
        echo"<h2>Titel van de site</h2><p>";
        echo $changetitle;
        echo "</p>";
    }
?>

<h1 id="controlpanel_site_template">Site uiterlijk</h1>
<?php 
    if (isset($usertemplates) OR isset($wi3templates)) {
        echo "<h2>template</h2><p id='template_picker'>";
        if (isset($usertemplates)) {
            echo "Site templates: " . $usertemplates . "<br />";
        }
        if (isset($wi3templates)) {
            echo "Wi3 templates: " . $wi3templates . "";
        }
        echo "</p>";
    }

?>