//-------------------------------------------------------------
// Executes when full page is loaded (and as such, images etc are loaded as well)
//-------------------------------------------------------------
jQuery( function($) {
	
});


if (!wi3 || typeof(wi3) != "object") { var wi3 = {}; }
wi3.popup = {

    //-------------------------------------------------------------
    // functions to show and/or hide the fade-in-out popup
    show : function(pagedivid) {
        //show edit div with the same css-top as the current edited div has
        if (pagedivid) {  var top = $("#" + pagedivid).offset().top; } else { var top = 100; }
        $("#menu_editdiv").css("top", top);
        $("#menu_editdiv").show();
        $("#menu_editdiv_background").fadeIn("fast");
    },

    hide : function() {
        $("#menu_editdiv").hide();
        $("#menu_editdiv_background").fadeOut("fast");
    }
    //-------------------------------------------------------------
}

wi3.request = function(controller, args) {
	//tell user request is pending...
	//get amount of currently executing requests
	var amount = 0;
	if (!args) { args = {}; }
	//possibility to make every request uncachable
	//var myDate = new Date();
    //var timestamp = myDate.getTime();
	$.post(wi3.urlof.wi3 + controller, args,
	  function(data){
		
		/* there are three categories here, handled in order:
		data.scriptsbefore, which is an object with javascript that will be executed before everything else
		data.dom, which will contain an object with a few different types of dom-editing:
			- remove (removes a certain html element)
			- fill (fill an html element with some content)
			- copy (copy html element to another html element)
			- append (append some content to an html element)
			- prepend (append some content to an html element)
			these types contain objects with jquery selectors along with a parameter, depending on the type of action you choose
			(for example the destination div in the case of copy)
		data.responses, which is an object with a few key-value pairs
		data.scriptsafter, which is an object javascript that will be executed after everything else
		data.alert, which will alert a message in Purr style.
		*/
		
		for(var index in data.scriptsbefore) {
			try { eval(data.scriptsbefore[index]); } catch(e) {}
		}
		
		for(var type in data.dom) {
			if (type == "remove" || type == "delete") {
				for(var selector in data.dom[type]) {
					$(data.dom[type][selector]).remove();
				}
			} else if (type == "fill") {
				for(var selector in data.dom[type]) {
					$(selector).html(data.dom[type][selector]);
				}
			} else if (type == "fill_withfade") {
				for(var selector in data.dom[type]) {
					$(selector).fadeOut().html(data.dom[type][selector]).fadeIn();
				}
			} else if (type == "copy") {
				for(var selector in data.dom[type]) {
					$(selector).replaceWith($(data.dom[type][selector]).html());
				}
			} else if (type == "append") {
				for(var selector in data.dom[type]) {
					$(selector).append(data.dom[type][selector]);
				}
			} else if (type == "prepend") {
				for(var selector in data.dom[type]) {	
					$(selector).prepend(data.dom[type][selector]);
				}
			}
		}
		
		for(var index in data.scriptsafter) {
			try { eval(data.scriptsafter[index]); } catch(e) {}
		}
		
		if (data.alert) {
			if ($("#wi3_notification_top").attr("id")) {
			    workplace.alert(data.alert);
			} else {
				alert(data.alert);
			}
		}

	  }
	  , "json"
	 );
};

wi3.tree = {
    simpleTreeCollection : {},
    currentTree : function() {
        return wi3.tree.simpleTreeCollection.get(0);
    }
}

wi3.editing = {

}

wi3.tinymce = {
    toggle : function(id) {
	    if (!tinyMCE.get(id))
		    tinyMCE.execCommand('mceAddControl', false, id);
	    else
		    tinyMCE.execCommand('mceRemoveControl', false, id);
    },
    
    initialized : function() {
	    id = document.wi3_tinymce_lastinit;
	    document.wi3_tinymce_lastinit = "";
    },

    init : function(id, theme) {
	    if (theme) {} else { theme = "simple"; }
	    document.wi3_tinymce_lastinit = id;
	    //first, initialize the div to a tinymce instance
	    tinyMCE.init({
            mode : "exact",
            elements : id,
            //auto_resize : true,
            relative_urls : false, /* use absolute urls for images */
            /*auto_focus : id,*/
            theme : theme,
            body_id : "tinymce_editor",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,formatselect,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist",
            theme_advanced_buttons2 : "image,undo,redo,wi3_link,unlink,separator,code,separato",
            theme_advanced_buttons3 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "top",
            theme_advanced_statusbar_location : "none",
	        theme_advanced_resize_horizontal : 0,
            plugins: "advimage, wi3_link", //paste plugin causes difficulties...
            paste_retain_style_properties : "color",
            paste_auto_cleanup_on_paste : true,
            paste_remove_styles : true,
            paste_remove_spans : true,
            paste_strip_class_attributes : "all",
            theme_advanced_resizing : true,
            extended_valid_elements : "td[*],div[*],form[*],input[*],iframe[src|width|height|name|align|style],script[*],code[*]",
            setup : function(ed) {
			    //insert link should only be active when the user has text selected
			    ed.onEvent.add(function(ed, e) {
        		    if(tinyMCE.activeEditor.selection.getContent().length > 0) {
        		        tinyMCE.activeEditor.controlManager.setDisabled('wi3_link', false);
        		    } else {
        		        tinyMCE.activeEditor.controlManager.setDisabled('wi3_link', true);
        		    }
                });

		     }
        });
    },

    destroy : function(id) {
	    var ed = tinyMCE.get(id);
	    if (ed) {
		    ed.setProgressState(0); // Stop progress, if any
		    tinyMCE.execCommand('mceRemoveControl', false, id);
	    }
    }

}

wi3.dateNow = function() {
    var today = new Date();
    var datestring = "" + today.getFullYear() + 
    (today.getMonth() < 9 ? "0"+(today.getMonth()+1) : today.getMonth()) + 
    (today.getDate() < 10 ? "0"+today.getDate() : today.getDate()) + 
    (today.getHours() < 10 ? "0" + today.getHours() : today.getHours()) + 
    (today.getMinutes() < 10 ? "0" + today.getMinutes() : today.getMinutes()) + 
    (today.getSeconds() < 10 ? "0" + today.getSeconds() : today.getSeconds()) + "" +
    (today.getMilliseconds() < 10 ? "0" + today.getMilliseconds() : today.getMilliseconds());
    return datestring;
}

//this function makes sure a certain object (like wi3.some1.some2.some3) exists by creating the objects from left to right, if they do not exist yet
wi3.makeExist = function(dotstring) {
    var parts = dotstring.split(".");
    var workerstring = "";
    for(part in parts) {
        workerstring += (workerstring.length > 0 ? "." : "") + parts[part];
        //if this is not an object, create it
        if (typeof(eval(workerstring)) != "object") {
            eval(workerstring + " = { };");
        }
    }
}
