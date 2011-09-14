function component_forms_savesettings() {
    wi3.request("component_forms/savesettings/" + wi3.pagefiller.editing.currentFieldId, $("#component_forms_settings").serializeArray() );
    return false;
}  

function component_forms_add() {
    wi3.request("component_forms/add/" + wi3.pagefiller.editing.currentFieldId, $("#component_forms_add").serializeArray() );
    return false;
}   

function component_forms_edit(id) {
    $("#component_forms_addtype").val($("#component_forms_edit_info_type_" + id).html());
    $("#component_forms_addname").val($("#component_forms_edit_info_name_" + id).html());
    $("#component_forms_addtitle").val($("#component_forms_edit_info_title_" + id).html());
    $("#component_forms_addrequired").attr("checked", ( $("#component_forms_edit_info_required_" + id).html() == 1 ? true : false ) );
    $("#component_forms_addoptions").val($("#component_forms_edit_info_options_" + id).html());
}

function component_forms_moveup(elm) {
    var parent = $(elm).closest("tr");
    if (parent.prev("tr").attr("id")) {
        //there indeed is an element to move 'this'
        wi3.request("component_forms/move_up/" + wi3.pagefiller.editing.currentFieldId, { base: parent.attr("id"), to: parent.prev("tr").attr("id") });
    }
}   

function component_forms_movedown(elm) {
    var parent = $(elm).closest("tr");
    if (parent.next("tr").attr("id")) {
        //there indeed is an element to move 'this'
        wi3.request("component_forms/move_down/" + wi3.pagefiller.editing.currentFieldId, { base: parent.attr("id"), to: parent.next("tr").attr("id") });
    }
}   

function component_forms_remove(id) {
    wi3.request("component_forms/remove/" + wi3.pagefiller.editing.currentFieldId, {id : id} );
    return false;
}

function component_forms_change_thankyoumessage() {    
    wi3.request("component_forms/changethankyoumessage/" + wi3.pagefiller.editing.currentFieldId, { thankyoumessage: $("#component_forms_change_thankyoumessage").val() } );
    return false;
}   

function component_forms_attach_toggle() {
    $("#component_forms_addtype").bind("change", function() {
       if ($(this).val() == "select") {
            $("#component_forms_addoptions_wrapper").show();
       } else {
            $("#component_forms_addoptions_wrapper").hide();
       }
    });
}
