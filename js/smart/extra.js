/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
window.addEventListener("DOMContentLoaded", function(){
    replaceSeoText(100);
}, false);

function replaceSeoText(timeout){
    if (typeof jQuery != "undefined") {
        var src = $(".content.seo"),
            dst = $(".seo-container");
        if (src.length && dst.length) {
            dst.children(".container").html(src);
        } else {
            src.remove();
            dst.remove();
        }
    } else {
        setTimeout(function(){
            replaceSeoText(timeout);
        }, timeout);
    }
}