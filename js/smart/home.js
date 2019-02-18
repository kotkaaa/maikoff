/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(window).on("load", function(){
    $.map($(document).find(".homeslider"), function(element){
        var s = SwiperSlider.construct($(element).children(".swiper-container"), {
            autoHeight: true,
//            preventClicks: false,
//            preventClicksPropagation: false,
            navigation: {
                nextEl: $(element).find('.swiper-button-next'),
                prevEl: $(element).find('.swiper-button-prev'),
            },
            pagination: {
                el: $(element).find('.swiper-pagination'),
                clickable: true
            }
        });
    });
});