//make sure that this object exists (adding something to wi3.pagefiller while that object does not exist, does not work)
wi3.makeExist("wi3.pagefiller.components.component_example");
//now add our funtion(s) to this existing object(s)


wi3.pagefiller.components.component_example.initedit = function() {
    var prefix = "component_example";
    $("#" + prefix + "_edit_tabs").tabs();
    //TinyMCE on the textareas
    $("#"+prefix+"_add textarea, #"+prefix+"_edit textarea").each(function() {
        //give ID if none is attached yet
        if ($(this).attr("id").length < 1) {
            $(this).attr("id", "textarea_" + wi3.dateNow());
        }
        wi3.tinymce.init($(this).attr("id"), "simple");
    });
    
}

wi3.pagefiller.components.component_example.add = function() {
    var prefix = "component_example";
    //check for TinyMCE instances
    $("#"+prefix+"_add textarea").each(function() {
        //check if there is a tinyMCE instance of this textarea 
        if ("#"+$($(this).attr("id")+"_container.mceEditor").attr("id")) {
            $(this).val(tinyMCE.get($(this).attr("id")).getContent());
        }
    });
    wi3.request(prefix + "/add/" + wi3.pagefiller.editing.currentFieldId, $("#" + prefix + "_add").serializeArray() );
    return false;
}   

wi3.pagefiller.components.component_example.edit = function(id) {
    var prefix = "component_example";
    //show the edit form
    $("#"+prefix+"_edit").slideDown();
    //set the id in the edit-form
    $("#"+prefix+"_edit input[name='__id']").val("item_"+id);
    //get the <td> that contains the edit-information
    var td = $("#"+prefix+"_item_"+id+" td:last");
    var tdchildren = td.children("span");
    for(childindex in tdchildren) {
        var child = $(tdchildren[childindex]);
        $("#"+prefix+"_edit input[name='" + child.attr("name") + "']").val(child.html());
        $("#"+prefix+"_edit select[name='" + child.attr("name") + "']").val(child.html());
        $("#"+prefix+"_edit textarea[name='" + child.attr("name") + "']").html(child.html());
        //if there is any TinyMCE instance
        var id = $("#"+prefix+"_edit textarea[name='" + child.attr("name") + "']").attr("id");
        if ($("#"+id+"_container.mceEditor").attr("id")) {
            tinyMCE.get(id).setContent(child.html());
        }
    }
}

wi3.pagefiller.components.component_example.saveedit = function(id) {
    var prefix = "component_example";
    //check for TinyMCE instances
    $("#"+prefix+"_edit textarea").each(function() {
        //check if there is a tinyMCE instance of this textarea 
        if ("#"+$($(this).attr("id")+"_container.mceEditor").attr("id")) {
            $(this).val(tinyMCE.get($(this).attr("id")).getContent());
        }
    });
    wi3.request(prefix + "/edit/" + wi3.pagefiller.editing.currentFieldId, $("#" + prefix + "_edit").serializeArray() );
    return false;
}

wi3.pagefiller.components.component_example.moveup = function(elm) {
    var prefix = "component_example";
    var parent = $(elm).closest("tr");
    if (parent.prev("tr").attr("id")) {
        //there indeed is an element to move 'this' to
        wi3.request(prefix + "/moveup/" + wi3.pagefiller.editing.currentFieldId, { 'swapbase': parent.attr("id"), 'swapwith': parent.prev("tr").attr("id") });
    }
}   

wi3.pagefiller.components.component_example.movedown = function(elm) {
    var prefix = "component_example";
    var parent = $(elm).closest("tr");
    if (parent.next("tr").attr("id")) {
        //there indeed is an element to move 'this' to
        wi3.request(prefix + "/movedown/" + wi3.pagefiller.editing.currentFieldId, { 'swapbase': parent.attr("id"), 'swapwith': parent.next("tr").attr("id") });
    }
}  

wi3.pagefiller.components.component_example.remove = function(id) {
    var prefix = "component_example";
    wi3.request(prefix + "/remove/" + wi3.pagefiller.editing.currentFieldId, {id: id} );
    return false;
}
