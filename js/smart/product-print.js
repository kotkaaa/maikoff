/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var PrintAssortment = objAssortment.init_print();

$(function(){
    // Setup gallery
    window.Gallery.instance.params.effect = "slide";
    window.Gallery.instance.params.simulateTouch = true;
    window.Gallery.instance.params.allowTouchMove = true;
    window.Gallery.instance.update();
    // Init assortments
    PrintAssortment.setup();
    console.log($(".product-print-card"));
    // add events to add-to-cart buttons (prints)
    Basket.onAdd = function(idKey){
        $.map($(".product-print-card").find(".add-to-cart"), function(btn){
            if ($(btn).data("idkey")==idKey) $(btn).addClass("in-cart");
        }); if (!this.opened) this.open();
    };
    Basket.onDelete = function(idKey){
        $.map($(".product-print-card").find(".add-to-cart"), function(btn){
            if ($(btn).data("idkey")==idKey) $(btn).removeClass("in-cart");
        });
    };
    $(".product-print-card").off("click", "add-to-cart").off("change", ".product-form input[type=\"radio\"]");
    $(".product-print-card").on("click", ".add-to-cart", function(e){
        e.stopPropagation();
        var $btn  = $(this),
            idKey = $btn.data("idkey");
        if ($btn.hasClass("in-cart")) Basket.open();
        else Basket.add(idKey);
    }).on("change", ".product-form input[type=\"radio\"]", function(e){
        if (!$(this).is(":checked")) return false;
        var $card = $(".product-print-card"),
            $btn  = $card.find(".add-to-cart"),
            itemID = $card.data("item-id"),
            colorID = 0,
            sizeID = 0,
            idKey = null,
            sizeCost = 0,
            productPrice = parseFloat(str_trim($card.find(".product-price .price").data("price"))),
            colorOptions = $card.find(".product-form .select-color input[type=\"radio\"]"),
            sizeOptions = $card.find(".product-form .select-size input[type=\"radio\"]"),
            inCart = false;
        $.map(colorOptions, function(option){
            if ($(option).is(":checked")) {
                colorID = $(option).val();
                return;
            }
        });
        $.map(sizeOptions, function(option){
            if ($(option).is(":checked")) {
                sizeID   = $(option).val();
                sizeCost = parseFloat($(option).data("cost")) || 0;
                return;
            }
        });
        if (itemID && colorID && sizeID) {
            idKey = "pa"+itemID+"c"+colorID+"s"+sizeID;
            inCart = Basket.isSetKey(idKey);
            $btn.data("idkey", idKey).prop("data-idkey", idKey).attr("data-idkey", idKey);
            if (inCart) $btn.addClass("in-cart");
            else $btn.removeClass("in-cart");
        }
        // Update product price
        productPrice += (!isNaN(sizeCost) ? sizeCost : 0);
        $card.find(".product-price .price").text(number_format(productPrice, 0, ".", " ") + " ");
        console.log(sizeCost);
    });
});