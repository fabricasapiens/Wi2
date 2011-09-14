function component_loginform_change_thankyoumessage() {    
    wi3.request("component_loginform/changethankyoumessage/" + wi3.pagefiller.editing.currentFieldId, { thankyoumessage: $("#component_loginform_change_thankyoumessage").val() } );
    return false;
}   

function component_loginform_change_errormessage() {    
    wi3.request("component_loginform/changeerrormessage/" + wi3.pagefiller.editing.currentFieldId, { errormessage: $("#component_loginform_change_errormessage").val() } );
    return false;
} 
