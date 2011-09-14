//-------------------------------------------------------------
// Executes when full page is loaded (and as such, images etc are loaded as well)
//-------------------------------------------------------------
jQuery( function($) {
	if (wi3.routing.action == "menu") {
		//in "menu"
	    workplace.menu_pages_tree();	 			//for moving the pages around in the menu_pages page 
	} else if (wi3.routing.action == "files") {
	    //in "files"
	    workplace.files_files_tree();              //for moving files around in the files menu
	} else if (wi3.routing.action == "users") {
	    //in "users"
	    workplace.users_users_tree();              //for creating a nice user-list in the users menu
	}
	//make the ajax request indicator work
	 $("#wi3_ajax_menu #wi3_ajax_indicator").bind("ajaxSend", function(){
       var amount = ($(this).html()*1)+1;
        $(this).html(amount);
     }).bind("ajaxComplete", function(){
         var amount = ($(this).html()*1)-1;
         $(this).html(amount);
     }).bind("ajaxStart", function(){
         $(this).addClass("working");
     }).bind("ajaxStop", function(){
         $(this).removeClass("working");
     });

});


var workplace = {

    timeoutvar : null,

    alert : function(html) {
        //make use of the nice notification div on top
        //
        //clear the timeOut that hides the notification after a while
	    clearTimeout(workplace.timeoutvar);
		//copy top to bottom
		$("#wi3_notification_bottom").html($("#wi3_notification_top").html());
		//if top is visible, we need to show bottom and hide top
		if ($("#wi3_notification_top").is(":visible")) {
		    //hide top, and show bottom
		    $("#wi3_notification_bottom").show();
		    $("#wi3_notification_top").hide();
		}
		//copy new data to top, and show
		$("#wi3_notification_top").html(html).slideDown("fast",function() {$("#wi3_notification_bottom").hide()} );
		//set Timeout to hide the notification
		workplace.timeoutvar = setTimeout('$("#wi3_notification_top").slideUp()', 3000);
    },

    reload_iframe : function () {
        var iframe = $("#wi3_edit_iframe");
	    var temp = iframe.get(0).src;
	    iframe.get(0).src = "";
	    iframe.get(0).src = temp;
    },

    reload_page : function() {
	    var temp = document.location.href;
	    document.location.href = "";
	    document.location.href = temp;
    },
    
    simpleTreeCollection : {},
    currentTree : function() {
        return workplace.simpleTreeCollection.get(0);
    },

    menu_pages_tree : function() {
	
	    //enable drag/drop within the tree and display the tree in a nice manner
	    workplace.simpleTreeCollection = $('#menu_pages').simpleTree({
		    autoclose: false,
		    afterClick:function(node){
			    wi3.request("ajaxengine/startEditPageSettings/" + node.attr("id"));
		    },
		    afterDblClick:function(node){
			    alert("text-"+$('span:first',node).text());
		    },
		    afterMove:function(destination, source, pos){
			    if ($(destination).is("li") && $(destination).attr("id")) {
				    wi3.request("ajaxengine/movePageUnder/" + source.attr("id") + "/" + destination.attr("id"), { });
			    } else if ($(destination).next("li").attr("id")) {
				    wi3.request("ajaxengine/movePageBefore/" + source.attr("id") + "/" + destination.next("li").attr("id"), { });
			    } else if ($(destination).prev("li").prev("li").prev("li").attr("id")) {  //two more prev() because after the drag, the source itself is placed before destination and should be skipped in this traversal
				    wi3.request("ajaxengine/movePageAfter/" + source.attr("id") + "/" + destination.prev("li").prev("li").prev("li").attr("id"), { });
			    } //en anders jammer
		    },
		    whileDrag:function(li, dest) {
		    
		    },
		    afterDrag:function(li, dest) {
		        //if dropped somewhere within the prullenbak (bin), then delete the page
		        if (dest.closest("#wi3_prullenbak").attr("id")) {
		            wi3.request("ajaxengine/deletePage/" + li.attr("id"), { });
		        }
		        workplace.currentTree().delNode();
		    },
		    afterAjax:function()
		    {
			    //alert('Loaded');
		    },
		    animate:true,
		    docToFolderConvert:true
	    });

	    $('#menu_pages a').each(function() {
		    $(this).attr("old_href", $(this).attr("href"));
	    });
	    $('#menu_pages a').attr("href", "javascript:void(0)");
    },
    
    menu_pagesettings_enable : function() {
        //enable the tabs
        $("#menu_pagesettings_tabs").tabs('destroy'); //'reset'
        $("#menu_pagesettings_tabs").tabs();
    },

    files_files_tree : function() {
	
	    //enable drag/drop within the tree and display the tree in a nice manner
	    workplace.simpleTreeCollection = $('#files_files').simpleTree({
		    autoclose: false,
		    afterClick:function(node){
			    wi3.request("ajaxengine/startEditFileSettings/" + node.attr("id"));
		    },
		    afterDblClick:function(node){
			    alert("text-"+$('span:first',node).text());
		    },
		    afterMove:function(destination, source, pos){
			    if ($(destination).is("li") && $(destination).attr("id")) {
				    wi3.request("ajaxengine/moveFileUnder/" + source.attr("id") + "/" + destination.attr("id"), { });
			    } else if ($(destination).next("li").attr("id")) {
				    wi3.request("ajaxengine/moveFileBefore/" + source.attr("id") + "/" + destination.next("li").attr("id"), { });
			    } else if ($(destination).prev("li").prev("li").prev("li").attr("id")) {  //two more prev() because after the drag, the source itself is placed before destination and should be skipped in this traversal
				    wi3.request("ajaxengine/moveFileAfter/" + source.attr("id") + "/" + destination.prev("li").prev("li").prev("li").attr("id"), { });
			    } //en anders jammer
		    },
		    whileDrag:function(li, dest) {
		    
		    },
		    afterDrag:function(li, dest) {
		        //if dropped somewhere within the prullenbak (bin), then delete the dropped object
		        if (dest.closest("#wi3_prullenbak").attr("id")) {
		            wi3.request("ajaxengine/deleteFile/" + li.attr("id"), { });
		        }
		        workplace.currentTree().delNode();
		    },
		    afterAjax:function()
		    {
			    //alert('Loaded');
		    },
		    animate:true,
		    docToFolderConvert:false
	    });
	    
	    //remove the fake li's that are used to make folders always work when there are no children inside it
	    $('[action=remove]').prev("li").remove(); //remove the 'line' before it
	    $('[action=remove]').remove(); //remove the li itself

	    $('#menu_pages a').each(function() {
		    $(this).attr("old_href", $(this).attr("href"));
	    });
	    $('#menu_pages a').attr("href", "javascript:void(0)");
    },
    
    files_filesettings_enable : function() {
        //enable the tabs
        $("#files_filesettings_tabs").tabs('destroy'); //'reset'
        $("#files_filesettings_tabs").tabs();
    },    
    
    users_users_tree : function() {
        workplace.simpleTreeCollection = $('#users_users').simpleTree({
		    autoclose: false,
		    animate:true,
		    docToFolderConvert:false,
		    afterClick:function(node){
			    wi3.request("ajaxengine/startEditUserSettings/" + node.attr("id"));
		    }
		});
    },
    
    users_usersettings_enable : function() {
        //enable the tabs
        $("#users_usersettings_tabs").tabs('destroy'); //'reset'
        $("#users_usersettings_tabs").tabs();
    }
    
    
    /*,

    wi3_edit_page_settings : function(elm) {
	    wi3.request("engine/startEditPageSettings/" + $(elm).parent().parent().attr("id"), {});
    }*/
}
