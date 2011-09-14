<div id="component_forms_edit_tabs">
	<ul>
		<li><a href="#component_forms_edit_tabs-1">Wijzig form</a></li>
		<li><a href="#component_forms_edit_tabs-2">Resultaten</a></li>
	</ul>
	<div id="component_forms_edit_tabs-1">

        <?php
            
            //the button to add something to the form
            echo "<h2>Nieuw element</h2><p>";
                echo "<form id='component_forms_add' onSubmit='return component_forms_add();'><table>";
                    echo "<tr><td> type </td><td> <select name='component_forms_addtype' id='component_forms_addtype'><option value='text'>tekstveld</option><option value='textarea'>groot tekstveld</option><option value='select'>optielijst</option><option value='html'>statische tekst</option><option value='bool'>vinkje</option><option value='checkbox'>verplicht vinkje</option><option value='captcha'>captcha</option><option value='submit'>verzendknop</option></select></td></tr>";
                    echo "<tr><td> (unieke) elementnaam </td><td> <input  name='component_forms_addname'  id='component_forms_addname' /></td></tr>";
                    echo "<tr><td> titel </td><td> <input  name='component_forms_addtitle'  id='component_forms_addtitle' /></td></tr>";
                    echo "<tr><td> verplicht veld? </td><td> <input  type='checkbox' name='component_forms_addrequired'  id='component_forms_addrequired' /></td></tr>";
                    echo "<tr id='component_forms_addoptions_wrapper' style='display: none;'><td> opties (voor optielijst) <br /> optie1,optie2,...</td><td> <input  name='component_forms_addoptions'  id='component_forms_addoptions' /></td></tr>";
                    echo "<tr><td> </td><td><button onClick='return component_forms_add();'>toevoegen</button></td></tr>";
                echo "</table></form>";
            echo "</p>";
            
            echo "<h2>Moet formulier na invullen ge-emaild worden?</h2>";
            echo "<form id='component_forms_settings' onSubmit='return component_forms_savesettings();'><table>";
            $emailopt = $formsettings->component_forms_settings_emailopt;
                echo "<tr><td colspan='2'><input type='radio' name='component_forms_settings_emailopt' value='no' " . ($emailopt == "no" ? "checked='checked'" : "") . ">nee, niet mailen</input></td></tr>";
                echo "<tr><td colspan='2'><input type='radio' name='component_forms_settings_emailopt' value='tofixedaddress' " . ($emailopt == "tofixedaddress" ? "checked='checked'" : "") . ">ja, naar een vast email adres</input></td></tr>";
                echo "<tr><td>emailadres: </td><td><input type='text' name='component_forms_settings_emailaddress' value='" . $formsettings->component_forms_settings_emailaddress . "'/></td></tr>";
                echo "<tr><td></td><td><button>wijzig instellingen</button></td></tr>";
            echo "</table></form>";
            
            echo "<h2>Bericht na verzenden formulier</h2>";
            echo "<textarea id='component_forms_change_thankyoumessage' style='width: 100%; height: 120px;'>";
            echo $thankyoumessage;
            echo "</textarea><br /><button onClick='component_forms_change_thankyoumessage()'>wijzig bericht</button>";
            
            echo "<h2>Bestaande elementen</h2><table id='component_forms_existing'><tr><th>naam</th><th>actie</th><th>actie</th><th>actie</th></tr>";
            foreach($form as $naam => $array) {
                //we get the 'complete' sqlarraydata-part
                $part = $form->_arrayparts[$naam];
                $partdata = unserialize($part->arrayval);
                //this 'part' stores the seqnr and id of the part.
                echo "<tr id='component_forms_part_" . $partdata["name"] . "'><td>" . $naam . "</td><td><a href='javascript:void(0)' onClick='component_forms_edit(\"" . $partdata["name"] . "\");'>wijzigen</a></td><td><a href='javascript:void(0)' onClick='component_forms_moveup(this);'>naar boven</a></td><td><a href='javascript:void(0)' onClick='component_forms_movedown(this);'>naar onderen</a></td><td><a href='javascript:void(0)' onClick='component_forms_remove(\"" . $partdata["name"] .  "\");'><strong>verwijderen</strong></a></td>";
                //expose some information of the elements, so that they can be edited
                echo "<td style='display: none;'>";
                    echo "<span id='component_forms_edit_info_type_" . $partdata["name"] .  "'>" . $partdata['type'] . "</span>";
                    echo "<span id='component_forms_edit_info_name_" . $partdata["name"] .  "'>" . $naam . "</span>";
                    echo "<span id='component_forms_edit_info_title_" . $partdata["name"] .  "'>" . $partdata['title'] . "</span>";
                    echo "<span id='component_forms_edit_info_required_" . $partdata["name"] .  "'>" . ($partdata['required'] ? 1 : 0) . "</span>";
                    echo "<span id='component_forms_edit_info_options_" . $partdata["name"] .  "'>" . (isset($partdata['options']) ? $partdata['options'] : "") . "</span>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
                    

        ?>
        
    </div>
    <div id="component_forms_edit_tabs-2">
        <?php
        
            //fetch the total amount of results
            echo "totaal aantal resultaten: "  . count($results) . ".<br />";
            //fetch the first date and last date
            $mindatum = "99999999999999999999";
            $maxdatum = 0;
            foreach($results as $datum => $result) {
                if ($datum <= $mindatum) { $mindatum = $datum; }
                if ($datum >= $maxdatum) { $maxdatum = $datum; }
            }
            $startyearpos = substr($mindatum, 0, 4) + ((substr($mindatum, 4, 2)-1) /12);
            $endyearpos = substr($maxdatum, 0, 4)  + (substr($maxdatum, 4, 2) /12);; //do not subtract one month, as we want this date to be the 'top' of the path
            $datespan = $endyearpos - $startyearpos;
            //and stack the results together per 'datepart'
            //we have a certain amount of timeslices to show the results in
            //thus, every slice can have some amount of results in it
            $amountofslices = 100;
            $timeslices = array();
            $maxinslice = 0;
            foreach($results as $datum => $result) {
                $posinyear = substr($datum, 0, 4) + ((substr($datum, 4,2)-1) / 12) + (substr($datum, 6,2) / 12 / 31);
                $nr = ceil(($posinyear - $startyearpos) / ($datespan) * $amountofslices);
                $timeslices[$nr] = (isset($timeslices[$nr]) ? $timeslices[$nr]+1 : 1);
                if ($timeslices[$nr] > $maxinslice) {
                    $maxinslice = $timeslices[$nr];
                }
            }
            
            //now display all the timeslices with their respective amount of results
            echo "<div style='position: relative; width: 400px; height: 120px; overflow: hidden; border-bottom: 5px solid #aaa;'>";
            foreach($timeslices as $nr => $amount) {
                echo "<div style='position: absolute; background: #aaa; left:" . ($nr*4) . "px; width: 4px; top: " . (120 - ceil($amount/$maxinslice*100)) . "px; height: 100px;'></div>";
            }
            echo "</div>";
            //and display the start and end-date
            echo "<div style='position: relative; width: 400px; height: 20px; overflow: hidden;'>" . 
            substr($mindatum, 0, 4) . "-" . substr($mindatum, 4,2) . "-01" .
            "<div style='position: absolute; top: 0px; right: 0px;'>" . substr($maxdatum, 0, 4) . "-" . (substr($maxdatum, 4,2)+1) . "-01</div>" .
            "</div>";
            
            echo "bekijk <a target='_blank' href='" . Wi3::$urlof->wi3 . "component_forms/results/" . $field->id . "'>alle resultaten</a>.";
        
        ?>
    </div>
</div>