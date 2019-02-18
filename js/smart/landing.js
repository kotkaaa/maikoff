/* 
 * WebLife CMS
 * Created on 16.07.2018, 16:21:17
 * Developed by http://weblife.ua/
 */

$(function(){
    // setup inline callback form
    var $form = $(".callback-form-inline");
    $form.find('input[type="tel"]').inputmask({
        mask: "+38 999 999-99-99",
        greedy: false,
        definitions: {
            '*': {
                validator: "[0-9 ]",
                cardinality: 1,
                casing: "lower"
            }
        }
    });
    $form.ajaxForm({
        dataType: "json",
        data: {
            source_name: document.title,
            source_url: window.location.href
        },
        success: function(json){
            $form.find("button").prop("disabled", false);
            if (json.data) {
                for (var name in json.data) {
                    $form.find("input[name=\"" + name + "\"]").val(json.data[name]);
                }
            }
            if (json.messages) {
                $form.resetForm();
                $form.find(".flex, .hide").addClass("hidden");
                $form.find(".thanks").removeClass("hidden");
                // send GA event
                send_event("Успешно отправлено", "Форма просчета");
            }
        },
        beforeSubmit: function(){
            $form.find("button").prop("disabled", true);
            // send GA event
            send_event("Нажатие", "Форма просчета");
        }
    });
    // setup inline callback form
    var $form2 = $(".feedback-form-inline");
    $form2.find('input[type="tel"]').inputmask({
        mask: "+38 999 999-99-99",
        greedy: false,
        definitions: {
            '*': {
                validator: "[0-9 ]",
                cardinality: 1,
                casing: "lower"
            }
        }
    });
    $form2.ajaxForm({
        dataType: "json",
        data: {
            source_name: document.title,
            source_url: window.location.href
        },
        success: function(json){
            $form2.find("button").prop("disabled", false);
            if (json.data) {
                for (var name in json.data) {
                    $form2.find("input[name=\"" + name + "\"]").val(json.data[name]);
                }
            }
            if (json.messages) {
                $form2.resetForm();
                $form2.parent().find("form, p, h3").not(".thank").addClass("hidden");
                $form2.parent().find(".thank").removeClass("hidden");
                // send GA event
                send_event("Успешно отправлено", "Форма просчета");
            }
        },
        beforeSubmit: function(){
            $form2.find("button").prop("disabled", true);
            // send GA event
            send_event("Нажатие", "Форма просчета");
        }
    });
    // setup tabs
    $(".tabs").on("click", ".tab", function(){
        var $this = $(this),
            index = $this.data("index");
        $this.siblings(".tab").removeClass("active");
        $this.addClass("active");
        $.map($(".tab-item"), function(tab_item, i){
            if (i == index) {
                $(tab_item).children(".swiper-container, .arrows").removeClass("hidden");
            } else $(tab_item).children(".swiper-container, .arrows").addClass("hidden");
        });
    });
    $(".top-banner.landing .anchor-links ul li").on("click","a", function (e) {
        e.preventDefault();
        var id  = $(this).attr('href'),
            top = $(id).offset().top-100;
        $('html').animate({scrollTop: top}, 900);
    });
});