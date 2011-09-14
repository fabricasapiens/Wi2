function component_imagegallery_viewimage(attr) {
    $("#component_imagegallery_" + attr.fieldid + "_image img").attr("src", attr.src);
    $("#component_imagegallery_" + attr.fieldid + "_image a").attr("href", attr.fullsrc);
    $("#component_imagegallery_" + attr.fieldid + "_image .component_imagegallery_currentthumbid").html(attr.thumbid);
}

function component_imagegallery_previousimage(elm) {
    var thumbid = $(elm).prevAll(".component_imagegallery_currentthumbid").html();
    $("#" + thumbid).prev("div").click();
}

function component_imagegallery_nextimage(elm) {
    var thumbid = $(elm).prevAll(".component_imagegallery_currentthumbid").html();
    $("#" + thumbid).next("div").click();
}
