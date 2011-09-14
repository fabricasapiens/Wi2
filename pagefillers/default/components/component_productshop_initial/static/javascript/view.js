function component_productshop_viewimage(attr) {
    $("#component_productshop_" + attr.fieldid + "_image img").attr("src", attr.src);
    $("#component_productshop_" + attr.fieldid + "_image a").attr("href", attr.fullsrc);
    $("#component_productshop_" + attr.fieldid + "_image .component_productshop_currentthumbid").html(attr.thumbid);
}

function component_productshop_previousimage(elm) {
    var thumbid = $(elm).prevAll(".component_productshop_currentthumbid").html();
    $("#" + thumbid).prev("div").click();
}

function component_productshop_nextimage(elm) {
    var thumbid = $(elm).prevAll(".component_productshop_currentthumbid").html();
    $("#" + thumbid).next("div").click();
}
