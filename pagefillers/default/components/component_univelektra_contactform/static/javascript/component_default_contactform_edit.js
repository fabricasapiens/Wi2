function wi3_component_default_contactform_save() {
    wi3.request("engine/stoppedEditField/" + document.wi3_currently_edited_fieldid, { emailaddress: $("#wi3_component_default_contactform_email").val() } );
    return false; //do not submit form
}
