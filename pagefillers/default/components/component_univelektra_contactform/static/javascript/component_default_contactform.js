function wi3_component_default_contactform_send_message(form) {
	var fieldid = $(form).closest(".wi3_field").attr("id");
    wi3.request("component_univelektra_contactform/send_message/" + fieldid, { 'name': $(form).children("#wi3_component_default_contactform_name").val(), 'email': $(form).children("#wi3_component_default_contactform_e").val(), 'subject': $(form).children("#wi3_component_default_contactform_subject").val(), 'message': $(form).children("textarea").val() } );
    return false; //do not submit form
}
