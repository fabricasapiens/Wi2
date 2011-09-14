//make sure that this object exists (adding something to wi3.pagefiller while that object does not exist, does not work)
wi3.makeExist("wi3.pagefiller.components.component_webshop");
//now add our funtion(s) to this existing object(s)


$(document).ready(function() {
    var prefix = "component_webshop";
    //present the first of the products with additional information
    $(".component_webshop_item_additional:first").slideDown("fast");
    //on mouseover, hide all the 'additional' information, except the info of the current article
    $(".component_webshop_item").bind("mouseover", function() {
        $(this).parent().find(".component_webshop_item_additional").not($(this).find(".component_webshop_item_additional")).slideUp("fast");
        $(this).find(".component_webshop_item_additional").slideDown("fast");
    });
    $(".component_webshop_addtocart > button, .component_webshop_orderform_showit").fancybox({
		hideOnContentClick: false,
		type: "html",
		title: "bestelformulier",
		titleShow: false,
		content: $(".component_webshop_orderformcontainer").html()
	});
	//check if an order was already placed (either succesfully or not)
	if ($(".component_webshop_orderform_showit").length > 0) {
        $(".component_webshop_orderform_showit").click();
	}
});

wi3.pagefiller.components.component_webshop.addtocart = function(itemid) {
    var prefix = "component_webshop";
    //check for TinyMCE instances
    $("#"+prefix+"_add textarea.componentInput_tinyMCE").each(function() {
        //check if there is a tinyMCE instance of this textarea 
        if ("#"+$($(this).attr("id")+"_container.mceEditor").attr("id")) {
            $(this).val(tinyMCE.get($(this).attr("id")).getContent());
        }
    });
    //increase the amount of this article to 1, if it was 0. Otherwise, just leave it at the existing value
    //$("#fancybox-inner").html($(".component_webshop_orderformcontainer").html());
    setTimeout("wi3.pagefiller.components.component_webshop.countfunc('"+itemid+"')", 10);
    return false;
}

wi3.pagefiller.components.component_webshop.countfunc = function(itemid) {
    $("select[name='" + itemid +"']").val('1'); 
}

wi3.pagefiller.components.component_webshop.order = function() {
    //wi3.request(prefix + "/add/" + wi3.pagefiller.editing.currentFieldId, $("#" + prefix + "_add").serializeArray() );
    return false;
}
