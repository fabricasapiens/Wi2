
    function wi3_component_afbeelding_choose(afbeeldingid) {
	    id = wi3.pagefiller.editing.currentFieldId;
	    wi3.request("pagefiller_default_ajax/stoppedEditField/"+ id, { 
		    afbeeldingid: afbeeldingid, 
	    });

        return false; //do not submit form
    }
