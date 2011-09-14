//#####GLOBAL VARIABLES
//define the edit menu
wi3_editmenu = "<div id='wi3_editmenu' style='display:none; font: 13px arial, tahoma; color: #000; position: absolute; z-index: 99; opacity: 0.9; background: #fff;'>";
//add the field-edit-options
wi3_editmenu += "<div id='wi3_editmenu_field'><div style='background: #ffff00; font-transformation: bold; color: #000; padding: 10px; padding-left: 20px;'><a href='javascript:void(0)' onClick='wi3_edit_field()'>wijzig veld</a></div></div>";
//add the fieldzone-options
wi3_editmenu += "<div id='wi3_editmenu_fieldzone'><div style='background: #000; font-transformation: bold; color: #fff; padding: 5px; padding-left: 10px;'>fieldzone</div><div style='padding: 10px;'>";
wi3_editmenu += " <a href='javascript:void(0)' onClick='wi3_add_field_to_dropzone($(this).prev().attr(\"value\"))'>add</a></div></div></div>";
//edit-border, the yellow border around a field
wi3_editborder = "<div id='wi3_editborderleft' style='background: #ffff00; position: absolute; display:none;'></div>";
wi3_editborder += "<div id='wi3_editbordertop' style='background: #ffff00; position: absolute; display:none;'></div>";
wi3_editborder += "<div id='wi3_editborderright' style='background: #ffff00; position: absolute; display:none;'></div>";
wi3_editborder += "<div id='wi3_editborderbottom' style='background: #ffff00; position: absolute; display:none;'></div>";
//edit-div, with background and cancel-button
wi3_editdiv = "<div id='wi3_editdiv_fancyboxelm' style='display: none;'></div><div id='wi3_editdiv' style='display:none;'><div id='wi3_editdiv_content'></div></div>";

var wi3_floatfix = '<style>.wi3_field:after {content: ".";display: block;height: 0;clear: both;visibility: hidden;} 	* html .wi3_field {	zoom: 1; }';

//#####LOADS WHEN THE **WHOLE** PAGE IS READY (ALSO THE IMAGES ETC, SO CORRECT DIMENSIONS CAN BE CALCULATED ON OBJECTS)
//DON'T USE DOCUMENT.READY() BECAUSE DOCUMENT.READY() WILL USUALLY BE TRIGGERED WHEN DOM IS READY, NOT NECCESARILY WHEN THE WHOLE PAGE IS LOADED.
jQuery( function($) {
	wi3_make_content_editable(); 	//create context-menu's for editing the content of the pages
});

//CUSTOM FUNCTIONS
function wi3_make_content_editable() {
	//add the editmenu and the editdiv
	$("body").append(wi3_editmenu); //the var wi3_editmenu is defined globally on top of this file
	$("body").append(wi3_editborder); //the var wi3_editborder is defined globally on top of this file
	$("body").append(wi3_editdiv); //the var wi3_editdiv is defined globally on top of this file
	currentheight = ($(parent.document).find("iframe").offset() != null ? $(parent.window).height() - $(parent.document).find("iframe").offset().top - 3 : $(window).height()) - ($.fn.fancybox.defaults.margin * 2);
	$("#wi3_editdiv_fancyboxelm").fancybox({
        hideOnContentClick: false,
	    type: "html",
	    autoDimensions: false,
	    scrolling: "auto",
	    width: 800,
	    height: currentheight,
	    content: function() { return $("#wi3_editdiv").html(); } //make it a function so that the up-to-date content is always showed, and not the initial content when running this sentence in init
    });
	$("body").append(wi3_floatfix); //this makes floating elements (like images) keep their 'size' in the flow of the page
	//var editbar = "<div id='wi3_editbar' style=' position: absolute; z-index: 100; opacity: 0.9; width: 3px; background: #6B913D;'></div>";
	//$("body").append(editbar);
	
	//make menu's appear on the dropzones (for fieldzone-actions like adding pages)
	//ONLY do this IF we are in advanced mode...
	if ($("#wi3_page_editmode").text() == "advanced") {
		$('.dropzone').each(function(a) {
			$(this).bind("mouseover", function(e){
				//only do something if the edit_menu is not already active somewhere (possibly in a contained child-wi3-field)
				if ($('#wi3_editmenu').css("display") != "block") {
					
					$('#wi3_editmenu').css("left", ($(this).offset().left - $("body").offset().left - $('#wi3_editmenu').width()));
					$('#wi3_editmenu').css("top", ($(this).offset().top - $("body").offset().top));
					$('#wi3_editmenu_fieldzone').css("display", "block"); //show fieldzone stuff
					$('#wi3_editmenu_field').css("display", "none"); //hide field-specific stuff
					$('#wi3_editmenu').css("display", "block");
					$('#wi3_editmenu').get(0).dropzone = this; //we want to set the dropzone on the Element itself, NOT on the JQuery representation, so use .get(0)
					//position show edit-bar
					//$('#wi3_editbar').css("left", ($(this).offset().left - $("body").offset().left - $('#wi3_editbar').width()));
					//$('#wi3_editbar').css("top", ($(this).offset().top - $("body").offset().top));
					//$('#wi3_editbar').height($(this).height());
					//$('#wi3_editbar').css("display", "block"); //show editbar
				}
			});
			$(this).bind("mouseout", function(e){
				if($(e.relatedTarget).closest("#wi3_editmenu").attr("id")) { //check if the cursor moves to a div where the parent is the edit menu
					//mouse is currently on the edit menu... don't hide!
					//just set that it should hide once mouse goes off the edit menu
					$("#wi3_editmenu").get(0).hideWhenMouseOut = true;
				} else {
					$('#wi3_editmenu').css("display", "none");
				}
			});
		});
	
	} //end of IF edit-mode is advanced

	//edit-menu mouse out
	$('#wi3_editmenu').bind("mouseout", function(e){
		//if mouse out on the edit menu, hide the menu ONLY when we entered the edit menu from a dropzone
		//in the dropzone, this will be set with 'hideWhenMouseOut' if the case...
		//also, make sure we do not go to this very same editmenu (that can happen with nested divs etc), because we do not want to hide the menu while still with the mouse in the editmenu
		if (this.hideWhenMouseOut && this.hideWhenMouseOut == true && !$(e.relatedTarget).closest("#wi3_editmenu").attr("id")) {
			$(this).css("display", "none");
			$('#wi3_editborderleft').css("display", "none");
			$('#wi3_editbordertop').css("display", "none");
			$('#wi3_editborderright').css("display", "none");
			$('#wi3_editborderbottom').css("display", "none");
			this.hideWhenMouseOut = false; //reset
		}
	});
	
	//make fields editable
	wi3_make_fields_editable();
	
	//make the user's site navigation work in edit-mode
	wi3_make_insite_navigation_work();
}

function wi3_make_fields_editable() {
	//make menu's appear on the dropzones
	$('.wi3_field, .wi3_siteField').each(function() {
		$(this).hover( function(e){
			
			var editborderwidth = 10;
			$('#wi3_editborderleft').css("left", $(this).offset().left - $("body").offset().left - editborderwidth);
			$('#wi3_editborderleft').css("width", editborderwidth ); //$(this).width()
			$('#wi3_editborderleft').css("top", $(this).offset().top - $("body").offset().top - editborderwidth );
			$('#wi3_editborderleft').css("height", $(this).outerHeight() + (2*editborderwidth) );
			$('#wi3_editborderleft').css("display", "block");
			
			$('#wi3_editbordertop').css("left", $(this).offset().left - $("body").offset().left - editborderwidth);
			$('#wi3_editbordertop').css("width", $(this).outerWidth() + (2*editborderwidth) ); //$(this).width()
			$('#wi3_editbordertop').css("top", $(this).offset().top - $("body").offset().top - editborderwidth );
			$('#wi3_editbordertop').css("height", editborderwidth );
			$('#wi3_editbordertop').css("display", "block");
			
			$('#wi3_editborderright').css("left", $(this).offset().left - $("body").offset().left + $(this).outerWidth() );
			$('#wi3_editborderright').css("width", editborderwidth ); //$(this).width()
			$('#wi3_editborderright').css("top", $(this).offset().top - $("body").offset().top - editborderwidth );
			$('#wi3_editborderright').css("height", $(this).outerHeight() + (2*editborderwidth) );
			$('#wi3_editborderright').css("display", "block");
			
			$('#wi3_editborderbottom').css("left", $(this).offset().left - $("body").offset().left - editborderwidth);
			$('#wi3_editborderbottom').css("width", $(this).outerWidth() + (2*editborderwidth) ); //$(this).width()
			$('#wi3_editborderbottom').css("top", $(this).offset().top - $("body").offset().top + $(this).outerHeight() );
			$('#wi3_editborderbottom').css("height", editborderwidth );
			$('#wi3_editborderbottom').css("display", "block");
			
			$('#wi3_editmenu_fieldzone').css("display", "none"); //hide fieldzone stuff
			$('#wi3_editmenu').css("left", ($(this).offset().left - $("body").offset().left + $(this).outerWidth() - $('#wi3_editmenu').outerWidth() ));
			$('#wi3_editmenu').css("top", ($(this).offset().top - $("body").offset().top - editborderwidth));
			$('#wi3_editmenu_field').css("display", "block"); //show field-specific stuff
			$('#wi3_editmenu').css("display", "block");
			$('#wi3_editmenu').get(0).field = this; //we want to set the current field on the Element itself, NOT on the JQuery representation, so use .get(0)
			
			e.stopPropagation();
		}, function(e){
			//a hover-out is also triggered if we move to the 'edit field' button
			//however, we ONLY want to do something if we REALLY leave the whole field
			//so check for that
			if($(e.relatedTarget).closest("#wi3_editmenu").attr("id")) { //check if the cursor moves to a div where there is a parent that is the edit menu
				//mouse is currently on the edit menu... don't hide!
				//just set that it should hide once mouse goes off the edit menu
				$("#wi3_editmenu").get(0).hideWhenMouseOut = true;
			} else {
				$('#wi3_editmenu').css("display", "none");
				$('#wi3_editborderleft').css("display", "none");
				$('#wi3_editbordertop').css("display", "none");
				$('#wi3_editborderright').css("display", "none");
				$('#wi3_editborderbottom').css("display", "none");
			}
		});
		$(this).bind("dblclick", function(e){
			wi3_edit_field();
		});
	});
}

function wi3_make_insite_navigation_work() {
    $("a").each( function() {
        //wi3/sites/sitename/pagename/vars
        //gets into wi3/engine/edit_page/pagename/vars
        //furthermore, we don't want to catch wi3/sites/sitename/data/somefile
	    var pattern = /^(.*)?wi3\/sites\/(?!edit_page)(?:[^\/]+)\/(?!data\/.+)(.*)$/;
	    if (pattern.test(this.href) == true) {
		    this.href = this.href.replace(pattern, "$1wi3/engine/edit_page/$2");
	    }
    });
}
$(document).ajaxComplete( function(){
   //after each ajax call, update the a tags
   wi3_make_insite_navigation_work();
 });

function wi3_add_field_to_dropzone(type) {
	//get the dropzone
	var dropzone = $('#wi3_editmenu').get(0).dropzone;
	//get page id
	var pageid = $("#wi3_page_id").html();
	//add field to this dropzone 
	//request to controller engine/addFieldToDropzone/page-id/dropzone-id
	wi3.request("pagefiller_default_ajax/addFieldToDropzone/" + pageid + "/" + $(dropzone).attr("id"), {type : type});
}

function wi3_edit_field() {
	//get the current 'hovered' field (the .field is set when a user hovers over a field)
	var field = $('#wi3_editmenu').get(0).field;
	//set to the document what field is edited right now
	//is useful when a user ends the edit and we want to know what field he was editing upon
	wi3.pagefiller.editing.currentFieldId = $(field).attr("id"); //set the id of the field, not the field itself
	//edit field
	wi3.request("pagefiller_default_ajax/startEditField/" + $(field).attr("id"), {});
}

//-----------------------------
// pagefiller editing vars and functions
//-----------------------------
if (!wi3.pagefiller) { 
    wi3.pagefiller = {};
}
if (!wi3.pagefiller.editing) { 
    wi3.pagefiller.editing = {};
}
//function to reload the current field
wi3.pagefiller.editing.reloadCurrentField = function() {
    //reloads the currently edited field
    wi3.pagefiller.editing.reloadField(wi3.pagefiller.editing.currentFieldId.substr(10));
}
//function to reload a certain field
wi3.pagefiller.editing.reloadField = function(ormid) {
    //reloads a field
    wi3.request("pagefiller_default_ajax/reloadField/"+ormid);
}

    

function wi3_delete_field() {
	//get the current 'hovered' field (the .field is set when a user hovers over a field)
	var field = $('#wi3_editmenu').get(0).field;
	//delete field
	wi3.request("pagefiller_default_ajax/deleteField/" + $(field).attr("id"), {});
}


function wi3_editdiv_show() {
	//show edit div with the same css-top as the current edited div has
    $("#wi3_editdiv_fancyboxelm").click();
    //$.fancybox.resize();
}

function wi3_editdiv_hide() {
    $.fancybox.close();
	//$("#wi3_editdiv").fadeOut("fast");
	//$("#wi3_editdiv_background").fadeOut("fast");
}

function wi3_tinymce_init_textmodule(id, theme) {
    if (theme) {} else { theme = "simple"; }
    document.wi3_tinymce_lastinit = id;
    //get the css that is used in this page. We want the TinyMCE editor to use that CSS as well
    var css = "";
    var links = $("link");
    for(var index=0; index < links.length; index++) {
	    css += "," + $(links[index]).attr("href");
    }
    css = css.substring(1);
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
        theme_advanced_buttons2 : "image,undo,redo,wi3_link,unlink,separator,code,separator,opslaan",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "top",
        theme_advanced_statusbar_location : "none",
        theme_advanced_resize_horizontal : 0,
        plugins: "advimage, paste, wi3_link",
        paste_retain_style_properties : "color",
        paste_auto_cleanup_on_paste : true,
        paste_remove_styles : true,
        paste_remove_spans : true,
        paste_strip_class_attributes : "all",
        content_css : css, //kohana.site_domain +  'media/css/reset.css,' + kohana.site_domain +  'media/css/style.css',
        theme_advanced_resizing : true,
        extended_valid_elements : "tr[*], td[*],div[*],form[*],input[*],iframe[src|width|height|name|align|style],script[*],code[*]",
        oninit : "wi3_tinymce_initialized",
        setup : function(ed) {
		    // Add the custom save-button
		    ed.addButton('opslaan', {
			    title : 'Save',
			    image : wi3.urlof.wi3 + 'static/images/save.jpeg',
			    onclick : function() {
				    // Save all content and show progress 
				    ed.setProgressState(1); // Show progress
				    wi3.request("pagefiller_default_ajax/stoppedEditField/" + id, {data: ed.getContent()});	
			    }
		    });

		    //Function to fix iframe to document height
		    fitEditor = function(ed)
		    {
			    editorID = ed.id;
			    var tble, frame, doc, docHeight, frameHeight;
			
			    frame = document.getElementById(editorID+"_ifr");
			    if ( frame != null )
			    {
				    //get the document object
				    if (frame.contentDocument) doc = frame.contentDocument; 
				    else if (frame.contentWindow) doc = frame.contentWindow.document; 
				    else if (frame.document) doc = frame.document; 
				
				    if ( doc == null )
				    return;
				
				    //prevent the scrollbar from showing
				    doc.body.style.overflow = "hidden";
				
				    //Fixes the issue of the table leaving empty space below iframe
				    tble = frame.parentNode.parentNode.parentNode.parentNode;
				    tble.style.height = 'auto';
				
				    frameHeight = parseInt(frame.style.height);
				
				    //Firefox
				    if ( doc.height ) docHeight = doc.height;
				    //MSIE
				    else docHeight = parseInt(doc.body.scrollHeight);
				
				    //MAKE BIGGER
				    if ( docHeight > frameHeight ) frame.style.height = (docHeight + 20) + "px";
				    //MAKE SMALLER
				    //else if ( docHeight < frameHeight ) frame.style.height = Math.max((docHeight + 20), 100) + "px";
			    }
		    };
		
		    if (theme == "advanced") {
			    //add fitEditor function to tinyMCE events
			    ed.onSetContent.add( fitEditor );
			    ed.onChange.add( fitEditor );
			    ed.onKeyPress.add( fitEditor );
		    }
		
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
}

    function wi3_tinymce_initialized() {
	    id = document.wi3_tinymce_lastinit;
	    document.wi3_tinymce_lastinit = "";
	
	    //now, if we click outside this element, save it
	    $(document).unbind("click"); //deconnect old bindings with 'click' so that we will not get 5 click-listeners all saving content at the same time
        $(document).bind("click",
        	//click has bubbled up to the document root
        	function(e){ 
			    //stop further propagation
	        	e.stopPropagation(); 
	        	//send the content of the field to the server
	        	var ed = tinyMCE.get(id);
	        	if (ed) {
				    ed.setProgressState(1); // Show progress
				    wi3.request("pagefiller_default_ajax/stoppedEditField/" + id, {data: ed.getContent()});
			    }
        	}
        );
        //stop click-propagation if we're inside an edit-div
	    $(".defaultSkin").click(
        	function(e){ 
        		//check if there are new elements added to the DOM (like popup menu's etc)
        		//1 step recursive
			    $(".defaultSkin").click(
				    function(e){ 
					    //if there is any edit-panel as parent, just bubble up
					    if ($(e.currentTarget).parent().closest(".defaultSkin").attr("id")) { 
					    } else { //but if there's no edit-panel, stop Propagation here, so that the click will not arrive at the document root
						    e.stopPropagation(); 
					    }
				    }
			    );
        	
        		//if there is any edit-panel as parent, just bubble up to that edit-panel-parent
        		if ($(e.currentTarget).parent().closest(".defaultSkin").attr("id")) { 
				
        		} else { //but if there's no edit-panel as parent, stop Propagation here, so that the click will not arrive at the document root
				    e.stopPropagation(); 
				
			    }
        	}
        );
    }
