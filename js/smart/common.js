/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var objSwiperSlider = new CSwiperSlider(),
    SwiperSlider = objSwiperSlider.init(),
    objCallback = new CCallback(),
    Callback = objCallback.init(),
    objMenu = new MegaMenu(),
    MainMenu = objMenu.init(),
    MobileMenu = objMenu.init_mobile(),
    SubMenu = objMenu.init_sub(),
    objSearch = new LiveSearch(),
    Search = objSearch.init(),
    objBasket = new CBasket(),
    Basket = objBasket.init(),
    objModal = new CModal(),
    Modal = objModal.init(),
    objRequestModalForm = new RequestModalForm(),
    RequestForm = objRequestModalForm.init();

Modernizr.Detectizr.detect({
    // option for enabling HTML classes of all features (not only the true features) to be added
    addAllFeaturesAsClass: false,
    // option for enabling detection of device
    detectDevice: true,
    // option for enabling detection of device model
    detectDeviceModel: true,
    // option for enabling detection of screen size
    detectScreen: true,
    // option for enabling detection of operating system type and version
    detectOS: true,
    // option for enabling detection of browser type and version
    detectBrowser: true,
    // option for enabling detection of common browser plugins
    detectPlugins: true
});

$(function(){
    //init Plugin Nice Select
    $('.select').niceSelect();
    // init callback actions
    Callback.initialize();
    // init mobile menu
    MobileMenu.initialize();
    // init main (desktop) menu
    MainMenu.initialize();
    // init sub (desktop) menu
    SubMenu.initialize();
    // init search autocomplete
    Search.initialize();
    // init basket
    Basket.initialize();
    // init modal
    Modal.initialize();
    // spin edit click events
    $(document).on("click", ".spin-edit button", function(e){
        e.preventDefault();
        var $this = $(this),
            js_ready = $this.closest(".spin-edit").hasClass("js-ready"),
            plus  = $this.hasClass("spin-up"),
            minus = $this.hasClass("spin-down"),
            input = $this.siblings("input[type=\"text\"]"),
            minVal= $this.closest(".spin-edit").data("min-val") || 0,
            iVal  = parseInt(input.val()),
            nVal  = plus ? (iVal + 1) : (iVal - 1);
        if (!js_ready) return false;
        if (nVal >= minVal) input.val(nVal).change();
    }).on("change", ".spin-edit input[type=\"text\"]", function(e){
        e.preventDefault();
        var $this = $(this),
            js_ready = $this.closest(".spin-edit").hasClass("js-ready"),
            plus  = $this.siblings(".spin-up"),
            minus = $this.siblings(".spin-down"),
            minVal= $this.closest(".spin-edit").data("min-val") || 0,
            iVal  = parseInt($this.val());
        if (!js_ready) return false;
        if (iVal <= minVal) minus.addClass("spin-del");
        else minus.removeClass("spin-del");
    });
    /**
     * Замена изображения и ссылок товара в списке
     * при наведении на цвета
     */
    $(".product-grid").on("mouseenter", ".product-item .pic label", function(e){
        var $a = $(this),
            href = $a.data("href"),
            src  = $a.data("img-src"),
            text = $a.data("title"),
            item = $a.closest(".product-item");
        $a.closest(".pic").siblings(".pic").removeClass("checked");
        $a.closest(".pic").addClass("checked");
        item.find(".product-grid-name").text(text);
        item.find(".product-grid-name,.product-grid-image").prop("href", href);
        item.find(".product-grid-image").children("img").prop("src", src);
    }).on("mouseleave", ".product-item", function(e){
        var item = $(this),
            pic  = item.find(".pic[data-color-id=\"" + item.data("color-id") + "\"]"),
            src  = item.data("img-src"),
            text = item.data("title"),
            href = item.data("href");
        pic.siblings(".pic").removeClass("checked");
        pic.addClass("checked");
        item.find(".product-grid-name").text(text);
        item.find(".product-grid-name,.product-grid-image").prop("href", href);
        item.find(".product-grid-image").children("img").prop("src", src);
    });
    /**
     * Отложенная загрузка изображений
     */
    $("img.lazy").lazyload();
});
/**
 * Отложенная загрузка изображений
 * при обновлении контента аяксом
 */
$(document).ajaxSuccess(function(event, xhr, settings){
    setTimeout(function(){
        $("img.lazy").lazyload();
    }, 160);
});
$(window).on("scroll", function(){
    var scroll = verge.scrollY();
    if (scroll > 100) $(".header-container,.page-body").addClass("scroll");
    else $(".header-container,.page-body").removeClass("scroll");
    // scroll up button
    var scrollUp = document.getElementById('scrollup');
    if (typeof scrollUp != "undefined" && scrollUp.length) {
        if (scroll > 200) {
            scrollUp.style.display = 'block';
        } else {
            scrollUp.style.display = 'none';
        }
    }
}).on("resize", function(){
    var mw = getMobileWidth();
    if (mw > 960 && (typeof MobileFilters != "undefined" && MobileFilters.active)) MobileFilters.close();
    Search.adjust();
}).on("load",function(){
    /*detach default event for hash links*/
    $(document).delegate("a", "click", function (e) {
        var href = $(this).attr("href"),
            rexp = new RegExp("^\#([A-z0-9]+)?");
        if (href.match(rexp) !== null) {
            e.preventDefault();
            if (href.length > 1) {
                var anchor = href.substring(1),
                    target = document.getElementById(anchor);
                if ($(target).length) {
                    var offset = $(target).offset();
                    scrollTo(0, Math.floor(offset.top - 100));
                }
            } return false;
        }
    });
    $.map($(document).find(".brand-swiper-element"), function(element){
        var s = SwiperSlider.construct($(element).children(".swiper-container"), {
            slidesPerView: "auto",
            spaceBetween: 18,
            freeMode: true,
            preventClicks: false,
            preventClicksPropagation: false,
            navigation: {
                nextEl: $(element).find('.swiper-button-next'),
                prevEl: $(element).find('.swiper-button-prev'),
            }
        });
    });
    $.map($(document).find(".product-swiper-element"), function(element, i){
        $(element).children(".swiper-container").addClass("product-swiper-container-" + i);
        SwiperSlider.construct(".product-swiper-container-"+i, {
            slidesPerView: 6,
            spaceBetween: 2,
//            preventClicks: false,
//            preventClicksPropagation: false,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: $(element).find('.swiper-button-next'),
                prevEl: $(element).find('.swiper-button-prev'),
            },
            scrollbar: {
                el: $(element).find('.swiper-scrollbar'),
                draggable: true,
                hide: false,
            },
            breakpoints: {
                1700: {
                    slidesPerView: 5,
                },
                1440: {
                    slidesPerView: 4,
                },
                960: {
                    slidesPerView: 3,
                },
                640: {
                    slidesPerView: 2,
                },
            }
        });
    });
    $.map($(document).find(".pop-brand-swiper-element"), function(element){
        var s = SwiperSlider.construct($(element).children(".swiper-container"), {
            slidesPerView: "auto",
            preventClicks: false,
            spaceBetween: 40,
            preventClicksPropagation: false,
            navigation: {
                nextEl: $(element).find('.swiper-button-next'),
                prevEl: $(element).find('.swiper-button-prev'),
            },
            scrollbar: {
                el: $(element).find('.swiper-scrollbar'),
                draggable: true,
                hide: true,
            }
        });
    });
    $.map($(document).find(".print-swiper-element"), function(element){
        var s = SwiperSlider.construct($(element).children(".swiper-container"), {
            slidesPerView: 9,
            preventClicks: false,
            preventClicksPropagation: false,
            navigation: {
                nextEl: $(element).find('.swiper-button-next'),
                prevEl: $(element).find('.swiper-button-prev'),
            },
            breakpoints: {
                1690: {
                    slidesPerView: 8,
                },
                1520: {
                    slidesPerView: 7,
                },
                1380: {
                    slidesPerView: 6,
                },
                1200: {
                    slidesPerView: 5,
                },
                1024: {
                    slidesPerView: 4,
                },
                820: {
                    slidesPerView: 3,
                },
                640: {
                    slidesPerView: 2,
                },
            }
        });
    });
});

function RequestModalForm(){
    var Form = {
        form: null,
        btn_upload: null,
        btn_attach: null,
        div_attachments: null,
        div_items: null,
        cnt_items: 0,
        product_substrates: null,
        product_colors: null,
        max_files: 3,
        construct:function(){
            var self                = this;
            self.form               = $("#requestForm");
            self.btn_upload         = self.form.find("#requestFileUpload");
            self.btn_attach         = self.form.find(".btn-attach");
            self.div_attachments    = self.form.find(".attachments");
            self.div_items          = self.form.find(".selected-items");
            self.cnt_items          = self.div_items.children(".selected-item:not(.empty)").size();
            self.product_substrates = new Object();
            self.product_colors     = new Object();
        },
        init: function(){
            var self = this;
            self.construct();
            self.draw_instance();
        },
        draw_instance: function(){
            var self = this;
            // Add new item row
            self.form.on("click", ".btn-add-new", function(e){
                e.preventDefault();
                var cnt = self.div_items.children(".selected-item:not(.empty)").size();
                if (cnt==0) self.div_items.html("");
                self.addItemRow();
            });
            self.form.on("click", ".spin-del", function(e){
                e.preventDefault();
                $(this).closest(".selected-item").remove();
                var cnt = self.div_items.children(".selected-item:not(.empty)").size(),
                    empty_html  = "<div class=\"selected-item empty\">";
                    empty_html += "   <button class=\"btn btn-primary btn-xl btn-add-new\">Добавить новый тип товара</button>";
                    empty_html += "</div>";
                if (cnt==0) self.div_items.prepend(empty_html);
            });
            // Remove attachments
            self.div_attachments.on("click", "a", function(e){
                e.preventDefault();
                $(this).closest("span").remove();
                self.recalcUploadedFiles();
            });
            // upload files
            self.btn_upload.fileupload({
                url: "/interactive/ajax.php?zone=site&action=ajaxFileUpload",
                dataType: 'json',
                autoUpload: true,
                singleFileUploads: false,
            }).on("fileuploadstart", function(e){
                self.btn_attach.addClass("progress");
            }).on("fileuploaddone", function(e, data){
                var html = "";
                $.map(data.result.files, function (file, index) {
                    if(file.error) {
                        alert('Файл не соответствует требованиям или достигнут лимит файлов!');
                    } else if (file.name) {
                        html += "<span>";
                        html += "<input type=\"hidden\" name=\"attachments[]\" value=\"" + file.name + "\"/>" + file.name;
                        html += " <a href=\"#\">&times;</a>";
                        html += "</span>";
                    }
                });
                self.div_attachments.append(html);
                self.btn_attach.removeClass("progress");
                self.recalcUploadedFiles();
            });
            // Ajax form submit
            self.form.ajaxForm({
                dataType: "json",
                data: {
                    source_name: document.title,
                    source_url: window.location.href
                },
                success: function(json){
                    if (json.errors) {
                        for (var name in json.errors) {
                            self.form.find("input[name=\"" + name + "\"]").addClass("error");
                        } 
                    } 
                    if (json.output) {
                        Modal.replace(json.output);
                        // send GA event
                        send_event("Успешно отправлено", "Форма просчета");
                    }
                },
                beforeSubmit: function(){
                    self.form.find("input.error").removeClass("error");
                }
            });
        },
        recalcUploadedFiles: function(){
            var self = this,
                cnt = self.div_attachments.children("span").size();
            if (cnt >= self.max_files) self.btn_attach.prop("disabled", true);
            else self.btn_attach.prop("disabled", false);
        },
        addItemRow: function(){
            var self = this,
                html = "";
            html += "<div class=\"selected-item\">";
            html += "   <div class=\"flex\">";
            html += "       <div class=\"select-type\">";
            html += "           <div class=\"f-row item-type\">";
            html += "               <label class=\"f-label\">Тип товара под печать</label>";
            html += "               <select class=\"select-color wide\" name=\"items[" + self.cnt_items + "][type]\">";
            html += "                   <option value=\"\">Не выбрано</option>";
            for (var id in self.substrates) {
                html += "               <option value=\"" + self.substrates[id].title + "\">" + self.substrates[id].title + "</option>";
            }
            html += "               </select>";
            html += "           </div>";
            html += "           <div class=\"f-row f-row-flex\">";
            html += "               <div class=\"f-col item-color\">";
            html += "                   <label class=\"f-label\">Цвет одежды</label>";
            html += "                   <select class=\"select-color wide\" name=\"items[" + self.cnt_items + "][color]\">";
            html += "                       <option value=\"\">Не выбрано</option>";
            for (var id in self.product_colors) {
                html += "                   <option data-color=\"#" + self.product_colors[id].hex + "\" value=\"" + self.product_colors[id].title + "\">" + self.product_colors[id].title + "</option>";
            }
            html += "                   </select>";
            html += "               </div>";
            html += "               <div class=\"f-col item-qty\">";
            html += "                   <label class=\"f-label\">Количество</label>";
            html += "                   <div class=\"spin-edit js-ready\">";
            html += "                       <button class=\"spin-btn spin-down spin-del\"></button>";
            html += "                       <input type=\"text\" value=\"1\" name=\"items[" + self.cnt_items + "][qty]\">";
            html += "                       <button class=\"spin-btn spin-up\"></button>";
            html += "                   </div>";
            html += "               </div>";
            html += "           </div>";
            html += "       </div>";
            html += "       <div class=\"select-settings\">";
            html += "           <label class=\"f-label\">Нанести лого/принт</label>";
            html += "           <div class=\"position\">";
            html += "               <input type=\"checkbox\" class=\"hidden\" name=\"items[" + self.cnt_items + "][print][]\" id=\"front_" + self.cnt_items + "\" value=\"front\"/>";
            html += "               <label class=\"checkbox\" for=\"front_" + self.cnt_items + "\">спереди</label>";
            html += "               <div class=\"size\">";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][front]\" id=\"size_front_" + self.cnt_items + "_1\" value=\"A6\" checked/>";
            html += "                   <label class=\"radio\" for=\"size_front_" + self.cnt_items + "_1\">A6</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][front]\" id=\"size_front_" + self.cnt_items + "_2\" value=\"A5\"/>";
            html += "                   <label class=\"radio\" for=\"size_front_" + self.cnt_items + "_2\">A5</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][front]\" id=\"size_front_" + self.cnt_items + "_3\" value=\"A4\"/>";
            html += "                   <label class=\"radio\" for=\"size_front_" + self.cnt_items + "_3\">A4</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][front]\" id=\"size_front_" + self.cnt_items + "_4\" value=\"A3\"/>";
            html += "                   <label class=\"radio\" for=\"size_front_" + self.cnt_items + "_4\">A3</label>";
            html += "               </div>";
            html += "               <div class=\"hint\">";
//            html += "                   <a href=\"#\">Размер печати</a>";
            html += "               </div>";
            html += "           </div>";
            html += "           <div class=\"position\">";
            html += "               <input type=\"checkbox\" class=\"hidden\" name=\"items[" + self.cnt_items + "][print][]\" id=\"rear_" + self.cnt_items + "\" value=\"rear\"/>";
            html += "               <label class=\"checkbox\" for=\"rear_" + self.cnt_items + "\">на спине</label>";
            html += "               <div class=\"size\">";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][rear]\" id=\"size_rear_" + self.cnt_items + "_1\" value=\"A6\" checked/>";
            html += "                   <label class=\"radio\" for=\"size_rear_" + self.cnt_items + "_1\">A6</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][rear]\" id=\"size_rear_" + self.cnt_items + "_2\" value=\"A5\"/>";
            html += "                   <label class=\"radio\" for=\"size_rear_" + self.cnt_items + "_2\">A5</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][rear]\" id=\"size_rear_" + self.cnt_items + "_3\" value=\"A4\"/>";
            html += "                   <label class=\"radio\" for=\"size_rear_" + self.cnt_items + "_3\">A4</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][rear]\" id=\"size_rear_" + self.cnt_items + "_4\" value=\"A3\"/>";
            html += "                   <label class=\"radio\" for=\"size_rear_" + self.cnt_items + "_4\">A3</label>";
            html += "               </div>";
            html += "               <div class=\"hint\">";
//            html += "                   <a href=\"#\">Размер печати</a>";
            html += "               </div>";
            html += "           </div>";
            html += "           <div class=\"position\">";
            html += "               <input type=\"checkbox\" class=\"hidden\" name=\"items[" + self.cnt_items + "][print][]\" id=\"arm_" + self.cnt_items + "\" value=\"arm\"/>";
            html += "               <label class=\"checkbox\" for=\"arm_" + self.cnt_items + "\">на рукаве</label>";
            html += "               <div class=\"size\">";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][arm]\" id=\"size_arm_" + self.cnt_items + "_1\" value=\"A6\" checked/>";
            html += "                   <label class=\"radio\" for=\"size_arm_" + self.cnt_items + "_1\">A6</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][arm]\" id=\"size_arm_" + self.cnt_items + "_2\" value=\"A5\"/>";
            html += "                   <label class=\"radio\" for=\"size_arm_" + self.cnt_items + "_2\">A5</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][arm]\" id=\"size_arm_" + self.cnt_items + "_3\" value=\"A4\"/>";
            html += "                   <label class=\"radio\" for=\"size_arm_" + self.cnt_items + "_3\">A4</label>";
            html += "                   <input type=\"radio\" class=\"hidden\" name=\"items[" + self.cnt_items + "][arm]\" id=\"size_arm_" + self.cnt_items + "_4\" value=\"A3\"/>";
            html += "                   <label class=\"radio\" for=\"size_arm_" + self.cnt_items + "_4\">A3</label>";
            html += "               </div>";
            html += "               <div class=\"hint\">";
//            html += "                   <a href=\"#\">Размер печати</a>";
            html += "               </div>";
            html += "           </div>";
            html += "       </div>";
            html += "   </div>";
            html += "   <button class=\"btn btn-primary btn-xl btn-add-new\">Добавить новый тип товара</button>";
            html += "</div>";
            self.div_items.append(html);
            Modal.draw();
            self.cnt_items++;
        },
        removeItemRow: function(){
            var self = this;
        },
        setParams: function(params){
            var self = this;
            for (var key in params) {
                self[key] = params[key];
            }
        }
    };
    this.init = function(){
        return Form;
    };
};

function CModal(){
    var Modal = {
        selector: null,
        popup: null,
        instance: null,
        params: {
            hashTracking: false,
            closeOnOutsideClick: true
        },
        construct: function () {
            var self = this;
            self.selector = '[data-remodal-id="popup_modal"]';
            self.popup = $(self.selector);
        },
        initialize: function () {
            var self = this;
            self.construct();
            self.instance = self.popup.remodal(self.params);
        },
        open: function (url) {
            var self = this,
                response = self.load(url);
            if (typeof response != "undefined") {
                self.replace(response);
                self.instance.open();
            }
            // send GA event
            send_event("Нажатие", "Форма просчета");
        },
        close: function () {
            var self = this;
            self.destruct();
            self.instance.close();
        },
        load: function (url) {
            var self = this,
                $response = null;
            $.ajax({
                url: url,
                type: "GET",
                async: false,
                complete: function (response) {
                    if (response.status == 200) {
                        var html = response.responseText;
                        if (typeof html != "undefined") $response = html;
                    }
                },
                beforeSend: function(){
                    self.destruct();
                }
            }); return $response;
        },
        draw: function(){
            var self = this;
            self.popup.find(".select-color").colorSelect("destroy");
            self.popup.find(".select-color").colorSelect();
            self.popup.find("input[type=\"tel\"]").inputmask({
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
            self.popup.find('.btn-attach').tooltipster({
                content: $('#tooltip_attach_files'),
                contentCloning: true,
                theme: 'tooltipster-shadow'
            });
        },
        replace: function (html) {
            var self = this;
            self.popup.children(".response").html(html);
            self.draw();
        },
        destruct: function () {
            var self = this;
            self.popup.children(".response").html("");
        }
    };
    this.init = function () {
        return Modal;
    };
};

function CBasket(){
    var Basket = {
        empty: true,
        opened: false,
        button: null,
        widget: null,
        layout: null,
        checkout: null,
        shippingID: 0,
        price: 0,
        total_price: 0,
        shipping_price: 0,
        amount: 0,
        items: null,
        files: null,
        storage: new Object(),
        btn_upload: null,
        btn_upload_checkout: null,
        btn_attach: null,
        btn_attach_checkout: null,
        div_attachments: null,
        div_attachments_checkout: null,
        upload_options: {
            url: "/interactive/ajax.php?zone=site&action=basket&option=ajaxFileUpload",
            forceIframeTransport: true,
            autoUpload: true,
            sequentialUploads : true,
            singleFileUploads: false,
        },
        construct: function(){
            var self = this;
            self.button = $(".header-container").find(".btn-basket");
            self.widget = $(".header-container").find(".basket-dropdown");
            self.layout = $("#basketLayout");
            self.checkout = $("#basketCheckout");
            self.btn_upload      = self.widget.find("#basketFileUpload");
            self.btn_attach      = self.widget.find(".btn-attach");
            self.btn_upload_checkout = $("#checkoutFileUpload");
            self.btn_attach_checkout = $("#checkoutAttachBtn");
            self.div_attachments = self.widget.find(".attachments");
            self.div_attachments_checkout = $("#checkoutAttachments");
            var formData = window.localStorage.getItem("checkoutFormData");
            if (formData !== null) self.storage = JSON.parse(formData);
        },
        destruct: function() {
            var self = this;
            window.localStorage.setItem("checkoutFormData", JSON.stringify(self.storage));
            return true;
        },
        initialize: function(){
            var self = this;
            self.construct();
            self.button.off("click").off("mouseenter");
            self.button.on("click", function(e){
                e.stopPropagation();
                self.toggle();
            }).on("mouseenter", function(){
                $(this).addClass("hover-on");
                setTimeout(function(){
                    if (self.button.hasClass("hover-on") && !self.opened) self.open();
                }, 300);
            }).on("mouseleave", function(){
                $(this).removeClass("hover-on");
            });
            self.widget.off("click");
            self.widget.on("click", function(e){
                e.stopPropagation();
            });
            $("body").on("click", function(){
                if (self.opened) self.close();
            });
            // attach files
            self.btn_attach.off("click");
            self.btn_attach.on("click", function(e){
                e.preventDefault();
                self.btn_upload.trigger("click");
            });
            self.btn_attach_checkout.off("click");
            self.btn_attach_checkout.on("click", function(e){
                e.preventDefault();
                self.btn_upload_checkout.trigger("click");
            });
            // delete attached files
            self.div_attachments.off("click", "a");
            self.div_attachments.on("click", "a", function(e){
                e.preventDefault();
                var fileID = $(this).closest("span").data("file-id");
                self.deleteFile(fileID);
            });
            self.div_attachments_checkout.off("click", "a");
            self.div_attachments_checkout.on("click", "a", function(e){
                e.preventDefault();
                var fileID = $(this).closest("span").data("file-id");
                self.deleteFile(fileID);
            });
            // upload files
            self.btn_upload.fileupload({
                url: "/interactive/ajax.php?zone=site&action=basket&option=ajaxFileUpload",
                forceIframeTransport: true,
                autoUpload: true,
                sequentialUploads : true,
                singleFileUploads: false,
            }).on("fileuploadstart", function(e){
                self.btn_attach.addClass("progress");
            }).on("fileuploaddone", function(e, data){
                self.btn_attach.removeClass("progress");
                self.update();
                self.initialize();
            });
            // upload files
            self.btn_upload_checkout.fileupload(
                self.upload_options).on("fileuploadstart", function(e){
                self.btn_attach_checkout.addClass("progress");
            }).on("fileuploaddone", function(e, data){
                self.btn_attach_checkout.removeClass("progress");
                self.update();
                self.initialize();
            });
            // update on initial page load
            if (self.items === null) self.update(1);
            // quick checkout form init
            var form  = $(".basket-quick-ckeckout"),
                input = form.find("input[type=\"tel\"]"),
                btn   = form.find("button[type=\"submit\"]"),
                regExPhone = /^\+38\s(044|039|050|063|066|067|068|091|092|093|094|095|096|097|098|099|073)+([\d\s\-]{10})$/;
            input.inputmask({
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
            input.on("change blur keyup", function(){
                var iVal = $(input).val(),
                    iName = $(input).attr("name");
                self.storage[iName] = iVal;
                if (iVal.length==0 || iVal.match(regExPhone)==null) {
                    btn.prop("disabled", true);
                } else btn.prop("disabled", false);
                self.destruct();
            });
            if (array_key_exists("phone", self.storage)) {
                $(input).val(self.storage.phone).trigger("change");
            }
        },
        toggle: function(){
            var self = this;
            if (self.opened) self.close();
            else self.open();
        },
        open: function(){
            var self = this;
            self.beforeOpen();
            self.button.addClass("toggle-on");
            self.widget.removeClass("hidden");
            $("html,body").addClass("m-noscroll");
            self.opened = true;
        },
        close: function(){
            var self = this;
            self.beforeClose();
            self.button.removeClass("toggle-on");
            self.widget.addClass("hidden");
            $("html,body").removeClass("m-noscroll");
            self.opened = false;
        },
        beforeOpen: function(){
            var self = this;
            Search.close();
            Callback.close();
            MobileMenu.close();
        },
        beforeClose: function(){
            var self = this;
        },
        add: function(idKey, qty, recalc){
            var self = this,
                added = false;
            qty = parseInt(qty)||false;
            recalc = recalc||false;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                async: false,
                data: {
                    zone: "site",
                    action: "basket",
                    option: "add",
                    itemID: idKey,
                    qty: qty,
                    recalc: recalc
                },
                success: function(json){
                    added = true;
                    self.update();
                    self.onAdd(idKey);
                }
            }); return added;
        },
        del: function(idKey, qty){
            var self = this,
                deleted = false;
            qty = parseInt(qty)||1;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                async: false,
                data: {
                    zone: "site",
                    action: "basket",
                    option: "remove",
                    itemID: idKey,
                    qty: qty
                },
                success: function(json){
                    deleted = true;
                    self.update();
                    self.onDelete(idKey);
                }
            }); return deleted;
        },
        clear: function(){
            var self = this;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                async: false,
                data: {
                    zone: "site",
                    action: "basket",
                    option: "clear"
                },
                success: function(){
                    self.update();
                    self.onClear();
                }
            });
        },
        update: function(initial){
            var self = this;
            initial  = initial||0;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                async: false,
                data: {
                    zone: "site",
                    action: "basket",
                    option: "update",
                    initial: initial
                },
                success: function(json){
                    self.items = json.items;
                    self.price = parseFloat(json.price);
                    self.total_price = parseFloat(json.total_price);
                    self.shipping_price = parseFloat(json.shipping_price);
                    self.amount = parseInt(json.amount);
                    self.shippingID = json.shippingID;
                    self.empty = json.empty;
                    if (json.output) {
                        self.layout.html(json.output.layout);
                        self.widget.find("#basketTotal").text(number_format(self.price, 0, ".", " ") + " ");
                        self.button.children(".cnt").text(self.amount);
                        if (self.checkout.length) self.checkout.html(json.output.checkout);
                    }
                    if (json.files) {
                        self.files = json.files;
                        if (json.output) {
                            var html = "";
                            for (var fileID in json.files) {
                                html += "<span data-file-id=\"" + fileID + "\">";
                                html += json.files[fileID].title + " <a href=\"#\">&times;</a>";
                                html += "</span>";
                            } self.div_attachments.html(html);
                            if (self.div_attachments_checkout.length) {
                                html = "";
                                for (var fileID in json.files) {
                                    html += "<span data-file-id=\"" + fileID + "\">";
                                    html += json.files[fileID].name + " <a href=\"#\">&times;</a>";
                                    html += "</span>";
                                } self.div_attachments_checkout.html(html);
                            }
                        }
                    }
                    if (self.empty && self.opened) self.close();
                    self.button.prop("disabled", self.empty);
                    self.recalcUploadedFiles();
                    self.onUpdate();
                }
            });
        },
        isSetKey: function(idKey){
            var self = this;
            return array_key_exists(idKey, self.items);
        },
        deleteFile: function(fileID){
            var self = this;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                data: {
                    zone: "site",
                    action: "basket",
                    option: "deleteFile",
                    itemID: fileID
                },
                success: function(){
                    self.update();
                }
            });
        },
        recalcUploadedFiles: function(){
            var self = this,
                size = self.div_attachments.children("span").size();
            if (size >= self.max_files) {
                self.btn_attach.prop("disabled", true);
                self.btn_attach_checkout.prop("disabled", true);
            } else {
                self.btn_attach.prop("disabled", false);
                self.btn_attach_checkout.prop("disabled", false);
            }
        },
        setShipping: function(shippingID){
            var self = this;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                data: {
                    zone: "site",
                    action: "basket",
                    option: "setShipping",
                    itemID: shippingID
                },
                success: function(){
                    self.update();
                }
            });
        },
        setPayment: function(paymentID){
            var self = this;
            $.ajax({
                url: "/interactive/ajax.php",
                dataType: "json",
                data: {
                    zone: "site",
                    action: "basket",
                    option: "setPayment",
                    itemID: paymentID
                },
                success: function(){
                    self.update();
                }
            });
        },
        onAdd: function(){},
        onDelete: function(){},
        onUpdate: function(){},
        onClear: function(){}
    };
    this.init = function(){
        return Basket;
    };
};

function LiveSearch(){
    var Search = {
        opened: false,
        input: null,
        root: null,
        form: null,
        wrap: null,
        btn: null,
        construct: function(){
            var self   = this;
            self.form  = $("#qSearchForm");
            self.wrap  = self.form.closest(".form");
            self.root  = self.form.closest(".search");
            self.btn   = $(".btn-search");
            self.input = self.form.children("input[type=\"search\"]");
        },
        initialize: function(){
            var self = this;
            self.construct();
            self.root.on("click", function(e){
                e.stopPropagation();
            });
            self.btn.on("click", function(e){
                e.preventDefault();
                self.toggle();
            });
            self.form.on("click", ".btn-close", function(e){
                e.preventDefault();
                self.close();
            });
            $("body").on("click", function(){
                if (self.opened) self.close();
            });
            self.input.autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: self.form.attr("action"),
                        type: 'POST',
                        data: {'stext': request.term, 'livesearch': 1},
                        complete: function (response) {
                            if (response.status == 200) {
                                var html = response.responseText;
                                if (html.length) {
                                    self.wrap.append(html);
//                                    self.wrap.find(".results").jScrollPane({
//                                        autoReinitialise: true,
//                                        autoReinitialiseDelay: 200
//                                    });
                                    self.adjust();
                                }
                            }
                        },
                        beforeSend: function () {
                            self.clear();
                        }
                    });
                },
                minLength: 3,
                scrollIntoView: true,
                autoFocus: true,
                delay: 300,
            });
        },
        toggle: function(){
            var self = this;
            if (!self.opened) self.open();
            else self.close();
        },
        open: function(){
            var self = this;
            self.beforeOpen();
            $(".page-body").addClass("search-in");
//            $("html,body").addClass("noswipe");
            self.root.addClass("opened");
            self.wrap.removeClass("hidden");
            self.input.val("").focus();
            self.opened = true;
        },
        close: function(){
            var self = this;
            self.beforeClose();
            $(".page-body").removeClass("search-in");
//            $("html,body").removeClass("noswipe");
            self.root.removeClass("opened");
            self.wrap.addClass("hidden");
            self.opened = false;
        },
        beforeOpen: function(){
            var self = this;
            Callback.close();
            Basket.close();
        },
        beforeClose: function(){
            var self = this;
            self.clear();
        },
        clear: function(){
            var self = this;
            self.wrap.children(".live-search").remove();
        },
        adjust: function(){
            var self = this,
                div = self.wrap.find(".live-search"),
                wh = getMobileHeight();
            if (div.length) {
                var ih = div.height(),
                    oh = self.wrap.offset().top,
                    sh = self.input.height(),
                    bh = 15;
                div.css({
                    maxHeight: Math.floor(wh - (oh + bh + sh))
                });
            }
        }
    };
    this.init = function(){
        return Search;
    };
};

function MegaMenu(){
    var Menu = {
        active: false,
        root: null,
        enter_class: null,
        hover_class: null,
        construct: function(){
            var self = this;
            self.root = $(".desktop-menu");
            self.enter_class = "mouseenter";
            self.hover_class = "hover-in";
        },
        initialize: function(){
            var self = this;
            self.construct();
            self.root.on("mouseenter", ".level-more", function (e) {
                var $this = $(this),
                    timeout  = (self.active ? 0 : 500);
                $this.addClass(self.enter_class);
                setTimeout(function(){
                    self.openSubLevel($this)
                }, timeout);
            }).on("mouseleave", ".level-more", function (e) {
                var $this = $(this);
                $this.removeClass(self.enter_class);
                setTimeout(function(){
                    self.closeSubLevel($this);
                }, 500);
            }).on("mouseenter", "a", function(){
                var $this = $(this),
                    $image = $this.closest(".dropdown").find(".menu-hover-image"),
                    hImage = $this.data("hover-image");
                if ($image.length) {
                    var original = $image.data("original");
                    if (typeof hImage != "undefined") {
                        $image.prop("src", hImage);
                    } else $image.prop("src", original);
                }
            }).on("mouseleave", "a", function(){
                var $this = $(this),
                    $image = $this.closest(".dropdown").find(".menu-hover-image"),
                    hImage = $this.data("hover-image");
                if ($image.length) {
                    var original = $image.data("original");
                    $image.prop("src", original);
                }
            });
        },
        openSubLevel: function(li){
            var self = this,
                siblings = li.siblings("li");
            if (li.hasClass(self.enter_class)) {
                for (var i = 0; i < siblings.length; i++) {
                    var el = $(siblings[i]);
                    self.closeSubLevel(el, true);
                } li.addClass(self.hover_class);
                self.active = true;
                // Close search form
                Search.close();
                // Close callback
                Callback.close();
                // Close basket
                Basket.close();
            } return li.hasClass(self.hover_class);
        },
        closeSubLevel: function(li, force){
            force = force || false;
            var self = this;
            if (!li.hasClass(self.enter_class)) {
                li.removeClass(self.hover_class);
                if (!force) self.active = false;
            }
        }
    },
    MobileMenu = {
        active: false,
        controller: null,
        container: null,
        catalog: null,
        body: null,
        construct: function(){
            var self = this;
            self.controller = $(".header-container").find(".icon-nav");
            self.container = $(".mobile-menu");
            self.catalog = self.container.children(".catalog");
            self.body = $(".page-body");
        },
        initialize: function () {
            var self = this;
            self.construct();
            self.controller.on("click", function(e){
                e.preventDefault();
                e.stopPropagation();
                self.toggle();
            });
            self.container.swipe({
                swipeLeft: function (event, direction, distance, duration, fingerCount, fingerData, currentDirection) {
                    event.preventDefault();
                    if (distance >= 50 && !self.catalog.hasClass("shift")) self.toggle();
                    console.log("You swiped " + direction);
                },
                swipeRight: function (event, direction, distance, duration, fingerCount, fingerData, currentDirection) {
                    event.preventDefault();
                    if (distance >= 50 && self.catalog.hasClass("shift")) self.unshift();
                    console.log("You swiped " + direction);
                }
            });
            self.catalog.on("click", ".level-more > a", function (e) {
                var li = $(this).closest("li"),
                    hover = li.hasClass("hover-in"),
                    sublevel = li.children(".sublevel");
                if (sublevel.length) {
                    e.preventDefault();
                    li.siblings("li").removeClass("hover-in");
                    li.addClass("hover-in");
                    self.shift();
                }
            }).on("click", ".return", function (e) {
                e.preventDefault();
                var li = $(this).closest(".level-more"),
                    hover = li.hasClass("hover-in");
                if (hover) self.unshift();
            }).on("click", ".close", function (e) {
                e.preventDefault();
                self.toggle();
            });
            self.body.on("click", function(e){
                if (self.container.hasClass("shift")) self.toggle();
            });
        },
        toggle: function () {
            var self = this;
            if (self.active) self.close();
            else self.open();
        },
        open: function () {
            var self = this;
            if (!self.active) {
                self.beforeOpen();
                self.container.addClass("shift");
                self.body.addClass("turn-left");//.addClass("noswipe");
                $("html,body").addClass("noswipe");
                self.active = true;
            }
        },
        beforeOpen: function(){
            var self = this;
            // Remove page turn class
            self.body.removeClass("turn-down").removeClass("turn-left").removeClass("turn-right").removeClass("fixed");
            // Deactivate callback block
            Callback.close();
            // Close search form
            Search.close();
            // Close basket
            Basket.close();
        },
        close: function(){
            var self = this;
            if (self.active) {
                self.container.removeClass("shift");
                self.body.removeClass("turn-left");//.removeClass("noswipe");
                $("html,body").removeClass("noswipe");
                self.active = false;
            }
        },
        shift: function(){
            var self = this;
            self.catalog.addClass("shift");
        },
        unshift: function(){
            var self = this;
            self.catalog.removeClass("shift");
            self.catalog.find(".hover-in").removeClass("hover-in");
        }
    },
    SubMenu = {
        sections: null,
        form: null,
        wrap: null,
        construct: function(){
            var self = this;
            self.form = $(".submenu");
            self.sections = self.form.children(".section");
        },
        initialize: function(){
            var self = this;
            self.construct();
            self.form.find(".section-toggle").on("click", function(e){
                var el = $(this),
                    section = el.closest(".section");
                if (!el.hasClass("toggle-ready")) return true; // переход по ссылке если нет подкатегорий
                e.preventDefault();
                el.toggleClass("on");
                section.toggleClass("collapsed");
            });
            self.form.find(".level-more").children("a").on("click", function(e){
                e.preventDefault();
                var el = $(this),
                    section = el.closest(".section"),
                    li = el.closest(".level-more"),
                    list = section.find(".list");
                li.addClass("opened");
                list.addClass("shift");
            });
            self.form.find(".return").on("click", ".back", function(e){
                e.preventDefault();
                var el = $(this),
                    section = el.closest(".section"),
                    li = el.closest(".level-more"),
                    list = section.find(".list");
                li.removeClass("opened");
                list.removeClass("shift");
            }).on("click", "a", function(e){
                var el   = $(this),
                    href = el.attr("href");
                if (href.match(/^\#/)!=null) {
                    e.preventDefault();
                    el.siblings(".back").trigger("click");
                }
            });
        }
    };
    this.init = function(){
        return Menu;
    };
    this.init_mobile = function(){
        return MobileMenu;
    };
    this.init_sub = function(){
        return SubMenu;
    };
}

function CCallback(){
    var Callback = {
        button: null,
        dropdown: null,
        phones: null,
        form: null,
        result: null,
        opened: false,
        construct: function(){
            var self = this;
            self.button = $(".header-container").find(".btn-phone");
            self.dropdown = $(".header-container").find(".callback-dropdown");
            self.phones = self.dropdown.find(".list-phones");
            self.form = self.dropdown.find(".form");
            self.result = self.dropdown.find(".result");
        },
        initialize: function(){
            var self = this;
            self.construct();
            self.button.on("click", function(e){
                e.stopPropagation();
                self.toggle();
            });
            self.dropdown.on("click", function(e){
                e.stopPropagation();
            });
            $("body").on("click", function(){
                if (self.opened) self.close();
            });
        },
        toggle: function(){
            var self = this;
            if (self.opened) self.close();
            else self.open();
        },
        open: function(){
            var self = this;
            self.beforeOpen();
            self.button.addClass("cross");
            self.dropdown.removeClass("hidden");
            self.opened = true;
        },
        close: function(){
            var self = this;
            self.beforeClose();
            self.button.removeClass("cross");
            self.dropdown.addClass("hidden");
            self.opened = false;
        },
        beforeOpen: function(){
            var self = this;
            self.destruct();
            // Close search form
            Search.close();
            // Close basket
            Basket.close();
        },
        beforeClose: function(){
            var self = this;
            self.destruct();
        },
        go: function(step){
            var self = this;
            switch (step) {
                case 2:
                    self.phones.addClass("hidden");
                    self.form.removeClass("hidden");
                    self.result.addClass("hidden");
                    var $form = self.form.children("form"),
                        input = self.form.find("input[type=\"tel\"]"),
                        btn = input.next(".btn");
                    input.inputmask({
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
                    input.val("").focus();
                    input.on("keyup blur focusin", function(){
                        var val = $(this).val(),
                            rex = /^\+38 0(\d{2}) \d{3}\-\d{2}-\d{2}$/;
                        if (val.match(rex) == null) btn.prop("disabled", true);
                        else btn.prop("disabled", false);
                    });
                    $form.ajaxForm({
                        dataType: "json",
                        data: {
                            source_name: document.title,
                            source_url: window.location.href
                        },
                        success: function(json){
                            if (json.messages) self.go(3);
                        },
                        beforeSubmit: function(){
                            btn.prop("disabled", true);
                        }
                    });
                    // send GA event
                    send_event("Нажатие", "Обратный звонок");
                    break;
                case 3:
                    self.phones.addClass("hidden");
                    self.form.addClass("hidden");
                    self.result.removeClass("hidden");
                    // send GA event
                    send_event("Успешно отправлено", "Обратный звонок");
                    // close popup after 3 seconds
                    setTimeout(function(){
                        self.close();
                    }, 3000);
                    break;
                default:
                    self.phones.removeClass("hidden");
                    self.form.addClass("hidden");
                    self.result.addClass("hidden");
                    break;
            }
        },
        destruct: function(){
            var self = this;
            self.go(1);
        }
    };
    this.init = function(){
        return Callback;
    };
};

function CSwiperSlider(){
    var Slider = {
        slider: null,
        construct: function (slider, params) {
            var self = this;
            slider = slider || '.home-slider';
            params = params || {};
            self.slider = new Swiper(slider, params);
            return self.slider;
        }
    };
    this.init = function () {
        return Slider;
    };
};
/**
 * @description Получение текущей ориентации экрана устройства
 * @returns {String}
 */
function getOrientation() {
    return Modernizr.Detectizr.device.orientation;
}
/**
 * @description Gets native viewport width
 * @returns {Number}
 */
function getMobileWidth() {
    var or = getOrientation();
    return verge.viewportW();
}
/**
 * @description Gets native viewport height
 * @returns {Number}
 */
function getMobileHeight() {
    var or = getOrientation();
    return verge.viewportH();
}
/**
 * @description Detects webkit safari browser
 * @returns {Boolean}
 */
function isMobile() {
    var device = Modernizr.Detectizr.device;
    return device.type == "mobile";
}
/**
 * @description Detects webkit safari browser
 * @returns {Boolean}
 */
function isWebKit() {
    var device = Modernizr.Detectizr.device;
    if (device.browser == "safari" && (device.model == "iphone" || device.model == "ipad"))
        return true;
    return false;
}
/**
 * @description Detects device specification with all features
 * @returns {modernizr_L8.Modernizr.Detectizr.device|Modernizr.Detectizr.device}
 */
function getDevice() {
    return Modernizr.Detectizr.device;
}
/**
 * @description resize iframe to it's content
 * @param {type} obj
 * @returns {undefined}
 */
function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
}

function str_trim (str) {
    return str.toString().trim();
}
/*number format*/
function number_format(number, decimals, dec_point, thousands_sep) {
    var i, j, kw, kd, km;
    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ",";
    }
    if (thousands_sep == undefined) {
        thousands_sep = ".";
    }
    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }
    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "")
    return km + kw + kd;
}
/*n-format-for-RANG*/
(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        define([], factory);
    } else if ( typeof exports === 'object' ) {
        module.exports = factory();
    } else {
        window.wNumb = factory();
    }
}(function(){
    'use strict';
    var FormatOptions = [
	'decimals',
	'thousand',
	'mark',
	'prefix',
	'suffix',
	'encoder',
	'decoder',
	'negativeBefore',
	'negative',
	'edit',
	'undo'
    ];
    // Reverse a string
    function strReverse ( a ) {
        return a.split('').reverse().join('');
    }
    // Check if a string starts with a specified prefix.
    function strStartsWith ( input, match ) {
        return input.substring(0, match.length) === match;
    }
    // Check is a string ends in a specified suffix.
    function strEndsWith ( input, match ) {
        return input.slice(-1 * match.length) === match;
    }
    // Throw an error if formatting options are incompatible.
    function throwEqualError( F, a, b ) {
        if ( (F[a] || F[b]) && (F[a] === F[b]) ) {
            throw new Error(a);
        }
    }
    // Check if a number is finite and not NaN
    function isValidNumber ( input ) {
        return typeof input === 'number' && isFinite( input );
    }
    // Borrowed: http://stackoverflow.com/a/21323330/775265
    function toFixed ( value, exp ) {
        value = value.toString().split('e');
        value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp)));
        value = value.toString().split('e');
        return (+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp))).toFixed(exp);
    }
    // Accept a number as input, output formatted string.
    function formatTo ( decimals, thousand, mark, prefix, suffix, encoder, decoder, negativeBefore, negative, edit, undo, input ) {
        var originalInput = input, inputIsNegative, inputPieces, inputBase, inputDecimals = '', output = '';
        // Apply user encoder to the input.
        // Expected outcome: number.
        if ( encoder ) {
            input = encoder(input);
        }
        // Stop if no valid number was provided, the number is infinite or NaN.
        if ( !isValidNumber(input) ) {
            return false;
        }
        // Rounding away decimals might cause a value of -0
        // when using very small ranges. Remove those cases.
        if ( decimals !== false && parseFloat(input.toFixed(decimals)) === 0 ) {
            input = 0;
        }
        // Formatting is done on absolute numbers,
        // decorated by an optional negative symbol.
        if ( input < 0 ) {
            inputIsNegative = true;
            input = Math.abs(input);
        }
        // Reduce the number of decimals to the specified option.
        if ( decimals !== false ) {
            input = toFixed( input, decimals );
        }
        // Transform the number into a string, so it can be split.
        input = input.toString();
        // Break the number on the decimal separator.
        if ( input.indexOf('.') !== -1 ) {
            inputPieces = input.split('.');
            inputBase = inputPieces[0];
            if ( mark ) {
                inputDecimals = mark + inputPieces[1];
            }
        } else {
            // If it isn't split, the entire number will do.
            inputBase = input;
        }
        // Group numbers in sets of three.
        if ( thousand ) {
            inputBase = strReverse(inputBase).match(/.{1,3}/g);
            inputBase = strReverse(inputBase.join( strReverse( thousand ) ));
        }
        // If the number is negative, prefix with negation symbol.
        if ( inputIsNegative && negativeBefore ) {
            output += negativeBefore;
        }
        // Prefix the number
        if ( prefix ) {
            output += prefix;
        }
        // Normal negative option comes after the prefix. Defaults to '-'.
        if ( inputIsNegative && negative ) {
            output += negative;
        }
        // Append the actual number.
        output += inputBase;
        output += inputDecimals;
        // Apply the suffix.
        if ( suffix ) {
            output += suffix;
        }
        // Run the output through a user-specified post-formatter.
        if ( edit ) {
            output = edit ( output, originalInput );
        } return output;
    }
    // Accept a sting as input, output decoded number.
    function formatFrom ( decimals, thousand, mark, prefix, suffix, encoder, decoder, negativeBefore, negative, edit, undo, input ) {
        var originalInput = input, inputIsNegative, output = '';
        // User defined pre-decoder. Result must be a non empty string.
        if ( undo ) {
            input = undo(input);
        }
        // Test the input. Can't be empty.
        if ( !input || typeof input !== 'string' ) {
            return false;
        }
        // If the string starts with the negativeBefore value: remove it.
        // Remember is was there, the number is negative.
        if ( negativeBefore && strStartsWith(input, negativeBefore) ) {
            input = input.replace(negativeBefore, '');
            inputIsNegative = true;
        }
        // Repeat the same procedure for the prefix.
        if ( prefix && strStartsWith(input, prefix) ) {
            input = input.replace(prefix, '');
        }
        // And again for negative.
        if ( negative && strStartsWith(input, negative) ) {
            input = input.replace(negative, '');
            inputIsNegative = true;
        }
        // Remove the suffix.
        // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/slice
        if ( suffix && strEndsWith(input, suffix) ) {
            input = input.slice(0, -1 * suffix.length);
        }
        // Remove the thousand grouping.
        if ( thousand ) {
            input = input.split(thousand).join('');
        }
        // Set the decimal separator back to period.
        if ( mark ) {
            input = input.replace(mark, '.');
        }
        // Prepend the negative symbol.
        if ( inputIsNegative ) {
            output += '-';
        }
        // Add the number
        output += input;
        // Trim all non-numeric characters (allow '.' and '-');
        output = output.replace(/[^0-9\.\-.]/g, '');
        // The value contains no parse-able number.
        if ( output === '' ) {
            return false;
        }
        // Covert to number.
        output = Number(output);
        // Run the user-specified post-decoder.
        if ( decoder ) {
            output = decoder(output);
        }
        // Check is the output is valid, otherwise: return false.
        if ( !isValidNumber(output) ) {
            return false;
        } return output;
    }
    // Validate formatting options
    function validate ( inputOptions ) {
        var i, optionName, optionValue,
            filteredOptions = {};
        if ( inputOptions['suffix'] === undefined ) {
            inputOptions['suffix'] = inputOptions['postfix'];
        }
        for ( i = 0; i < FormatOptions.length; i+=1 ) {
            optionName = FormatOptions[i];
            optionValue = inputOptions[optionName];
            if ( optionValue === undefined ) {
                // Only default if negativeBefore isn't set.
                if ( optionName === 'negative' && !filteredOptions.negativeBefore ) {
                    filteredOptions[optionName] = '-';
                // Don't set a default for mark when 'thousand' is set.
                } else if ( optionName === 'mark' && filteredOptions.thousand !== '.' ) {
                    filteredOptions[optionName] = '.';
                } else {
                    filteredOptions[optionName] = false;
                }
            // Floating points in JS are stable up to 7 decimals.
            } else if ( optionName === 'decimals' ) {
                if ( optionValue >= 0 && optionValue < 8 ) {
                    filteredOptions[optionName] = optionValue;
                } else {
                    throw new Error(optionName);
                }
            // These options, when provided, must be functions.
            } else if ( optionName === 'encoder' || optionName === 'decoder' || optionName === 'edit' || optionName === 'undo' ) {
                if ( typeof optionValue === 'function' ) {
                    filteredOptions[optionName] = optionValue;
                } else {
                    throw new Error(optionName);
                }
            // Other options are strings.
            } else {
                if ( typeof optionValue === 'string' ) {
                    filteredOptions[optionName] = optionValue;
                } else {
                    throw new Error(optionName);
                }
            }
        }
        // Some values can't be extracted from a
        // string if certain combinations are present.
        throwEqualError(filteredOptions, 'mark', 'thousand');
        throwEqualError(filteredOptions, 'prefix', 'negative');
        throwEqualError(filteredOptions, 'prefix', 'negativeBefore');
        return filteredOptions;
    }
    // Pass all options as function arguments
    function passAll ( options, method, input ) {
        var i, args = [];
        // Add all options in order of FormatOptions
        for ( i = 0; i < FormatOptions.length; i+=1 ) {
                args.push(options[FormatOptions[i]]);
        }
        // Append the input, then call the method, presenting all
        // options as arguments.
        args.push(input);
        return method.apply('', args);
    }
    function wNumb ( options ) {
        if ( !(this instanceof wNumb) ) {
            return new wNumb ( options );
        }
        if ( typeof options !== "object" ) {
            return;
        }
        options = validate(options);
        // Call 'formatTo' with proper arguments.
        this.to = function ( input ) {
            return passAll(options, formatTo, input);
        };
        // Call 'formatFrom' with proper arguments.
        this.from = function ( input ) {
            return passAll(options, formatFrom, input);
        };
    } return wNumb;
}));

function array_key_exists (key, search) {
    if (!search || (search.constructor !== Array && search.constructor !== Object)){
        return false;
    } return search[key] !== undefined;
}

function count( mixed_var, mode ) {
    var key, cnt = 0;
    if( mode == 'COUNT_RECURSIVE' ) mode = 1;
    if( mode != 1 ) mode = 0;
    for (key in mixed_var){
        cnt++;
        if( mode==1 && mixed_var[key] && (mixed_var[key].constructor === Array || mixed_var[key].constructor === Object) ){
            cnt += count(mixed_var[key], 1);
        }
    } return cnt;
}

function strpos (haystack, needle, offset) {
    var i = haystack.indexOf( needle, offset ); // returns -1
    return i >= 0 ? i : false;
}

function empty(mixed_var) {
    return (mixed_var==="" || mixed_var===0 || mixed_var==="0" || mixed_var===null || mixed_var===false || mixed_var==='undefined' || typeof(mixed_var)==='undefined' || (is_array(mixed_var) && mixed_var.length===0));
}

function is_array( mixed_var ) {
    return ( mixed_var instanceof Array );
    }

function is_object( mixed_var ){
    if (mixed_var instanceof Array) {
        return false;
    } else {
        return (mixed_var !== null) && (typeof( mixed_var ) == 'object');
    }
}

function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key]===needle) || (!strict && haystack[key]==needle)) {
            found = true;
            break;
        }
    } return found;
}

function md5 ( str ) {	// Calculate the md5 hash of a string
	// 
	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	// + namespaced by: Michael White (http://crestidg.com)

	var RotateLeft = function(lValue, iShiftBits) {
			return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
		};

	var AddUnsigned = function(lX,lY) {
			var lX4,lY4,lX8,lY8,lResult;
			lX8 = (lX & 0x80000000);
			lY8 = (lY & 0x80000000);
			lX4 = (lX & 0x40000000);
			lY4 = (lY & 0x40000000);
			lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
			if (lX4 & lY4) {
				return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
			}
			if (lX4 | lY4) {
				if (lResult & 0x40000000) {
					return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
				} else {
					return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
				}
			} else {
				return (lResult ^ lX8 ^ lY8);
			}
		};

	var F = function(x,y,z) { return (x & y) | ((~x) & z); };
	var G = function(x,y,z) { return (x & z) | (y & (~z)); };
	var H = function(x,y,z) { return (x ^ y ^ z); };
	var I = function(x,y,z) { return (y ^ (x | (~z))); };

	var FF = function(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		};

	var GG = function(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		};

	var HH = function(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		};

	var II = function(a,b,c,d,x,s,ac) {
			a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
			return AddUnsigned(RotateLeft(a, s), b);
		};

	var ConvertToWordArray = function(str) {
			var lWordCount;
			var lMessageLength = str.length;
			var lNumberOfWords_temp1=lMessageLength + 8;
			var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
			var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
			var lWordArray=Array(lNumberOfWords-1);
			var lBytePosition = 0;
			var lByteCount = 0;
			while ( lByteCount < lMessageLength ) {
				lWordCount = (lByteCount-(lByteCount % 4))/4;
				lBytePosition = (lByteCount % 4)*8;
				lWordArray[lWordCount] = (lWordArray[lWordCount] | (str.charCodeAt(lByteCount)<<lBytePosition));
				lByteCount++;
			}
			lWordCount = (lByteCount-(lByteCount % 4))/4;
			lBytePosition = (lByteCount % 4)*8;
			lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
			lWordArray[lNumberOfWords-2] = lMessageLength<<3;
			lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
			return lWordArray;
		};

	var WordToHex = function(lValue) {
			var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
			for (lCount = 0;lCount<=3;lCount++) {
				lByte = (lValue>>>(lCount*8)) & 255;
				WordToHexValue_temp = "0" + lByte.toString(16);
				WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
			}
			return WordToHexValue;
		};

	var x=Array();
	var k,AA,BB,CC,DD,a,b,c,d;
	var S11=7, S12=12, S13=17, S14=22;
	var S21=5, S22=9 , S23=14, S24=20;
	var S31=4, S32=11, S33=16, S34=23;
	var S41=6, S42=10, S43=15, S44=21;

	str = this.utf8_encode(str);
	x = ConvertToWordArray(str);
	a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;

	for (k=0;k<x.length;k+=16) {
		AA=a; BB=b; CC=c; DD=d;
		a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
		d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
		c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
		b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
		a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
		d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
		c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
		b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
		a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
		d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
		c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
		b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
		a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
		d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
		c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
		b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
		a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
		d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
		c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
		b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
		a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
		d=GG(d,a,b,c,x[k+10],S22,0x2441453);
		c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
		b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
		a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
		d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
		c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
		b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
		a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
		d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
		c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
		b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
		a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
		d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
		c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
		b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
		a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
		d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
		c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
		b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
		a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
		d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
		c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
		b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
		a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
		d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
		c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
		b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
		a=II(a,b,c,d,x[k+0], S41,0xF4292244);
		d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
		c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
		b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
		a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
		d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
		c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
		b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
		a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
		d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
		c=II(c,d,a,b,x[k+6], S43,0xA3014314);
		b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
		a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
		d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
		c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
		b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
		a=AddUnsigned(a,AA);
		b=AddUnsigned(b,BB);
		c=AddUnsigned(c,CC);
		d=AddUnsigned(d,DD);
	}
	var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);
	return temp.toLowerCase();              
}

function send_event(action, label) {
    var data = ["_trackEvent", window.document.title + (window.gClid ? " (adwords)" : ""), action, label];   
    if (window._gaq) {
        _gaq.push(data);
    }
    //console.log('-------------------------------------------------------------------------');
    //console.log(data);
    return true;
}
