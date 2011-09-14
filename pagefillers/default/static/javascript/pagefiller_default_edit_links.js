
//#####LOADS WHEN THE **WHOLE** PAGE IS READY (ALSO THE IMAGES ETC, SO CORRECT DIMENSIONS CAN BE CALCULATED ON OBJECTS)
//DON'T USE DOCUMENT.READY() BECAUSE DOCUMENT.READY() WILL USUALLY BE TRIGGERED WHEN DOM IS READY, NOT NECCESARILY WHEN THE WHOLE PAGE IS LOADED.
jQuery( function($) {
	wi3_make_insite_navigation_work(); 	//make the page-links point to edit-pages
});

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

