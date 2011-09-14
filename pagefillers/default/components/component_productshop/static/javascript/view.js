// Make sure that this object exists (adding something to wi3.pagefiller while that object does not exist, does not work)
wi3.makeExist("wi3.pagefiller.components.component_productshop");
// Now add our funtion(s) to this existing object(s)

// User adds product to cart
wi3.pagefiller.components.component_productshop.addtocart = function(productselect) {
    var product = productselect.attr("product");
    var val = productselect.val();
    var fieldnr = productselect.attr("fieldnr");
    var prefix = "component_productshop";
    wi3.request(prefix + "/addtocart/", { fieldnr: fieldnr, productid: product, amount: val } );
    return false;
}

wi3.pagefiller.components.component_productshop.removefromcart = function(productid) {
    var prefix = "component_productshop";
    wi3.request(prefix + "/removefromcart/", { productid: productid } );
    return false;
}

wi3.pagefiller.components.component_productshop.removeonefromcart = function(productid) {
    var prefix = "component_productshop";
    wi3.request(prefix + "/removeonefromcart/", { productid: productid } );
    return false;
}

wi3.pagefiller.components.component_productshop.addonetocart = function(productid) {
    var prefix = "component_productshop";
    wi3.request(prefix + "/addonetocart/", { productid: productid } );
    return false;
}

wi3.pagefiller.components.component_productshop.order = function(form) {
    var prefix = "component_productshop";
    wi3.request(prefix + "/order/", $(form).serializeArray() );
    return false;
}

// Make cart-button work
$(document).ready(function(){
   var prefix = "component_productshop";
   $("#component_productshop_cart").fancybox({
        hideOnContentClick: false,
	    type: "html",
	    autoDimensions: false,
	    scrolling: "auto",
	    width: 400,
	    height: 400,
	    content: "<div id='component_productshop_cart_orderform'><div id='component_productshop_cart_waitfororderform'></div><div style='text-align: center;'>bezig met ophalen van winkelwagen</div></div>"
    });
    // Add another onClick to the cart button so we can load the cart overview and orderform
    $("#component_productshop_cart").click(function()
    {
        wi3.request(prefix + "/loadcartoverview/", {});
    }
    );
});
