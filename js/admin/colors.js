/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
    $("#colorsTable").children("tbody").sortable();

    $('.colorsort').on("click", ".more", function(){
        var li = $(this).closest('.colorsort');
        li.siblings('.colorsort').find(".clicked").removeClass("clicked");
        $(this).toggleClass('clicked');
    });
});

function updateColor(jscolor, itemID) {
    // 'jscolor' instance can be used as a string
    document.getElementById('prw_'+itemID).style.backgroundColor = '#' + jscolor;
}