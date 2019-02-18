/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var objGallery = new ProductGallery(),
    Gallery = objGallery.init(),
    objRelated = new ProductRelated(),
    Related = objRelated.init(),
    objAssortment = new CAssortment(),
    Assortment = objAssortment.init(),
    objShare = new CShare(),
    Share = objShare.init();

$(window).on("load", function(){
    // Init related products
    Related.initialize();
});

$(function(){
    // Init gallery
    Gallery.initialize();
    // Init assortments
    Assortment.setup();
    // Toggle hidden blocks
    $(".product-card").on("click", ".switch-toggle", function(){
        $(this).toggleClass("toggle-on");
    });
    // basket actions
    Basket.onAdd = function(idKey){
        var $btn = $(".product-card-standart").find(".add-to-cart"),
            caret = $btn.children(".cnt"),
            options = $(".product-card-standart .product-form .spin-edit input[type=\"text\"]"),
            cnt = 0;
        $.map(options, function(option){
            var optKey = $(option).data("idkey"),
                optVal = array_key_exists(optKey, Basket.items) ? Basket.items[optKey] : 0;
            $(option).val(optVal).change();
            cnt += parseInt(optVal);
        });
        if (cnt) {
            $btn.addClass("in-cart").data("cnt", cnt);
            caret.text(cnt).removeClass("hidden");
        } else {
            $btn.removeClass("in-cart").data("cnt", cnt);
            caret.text(cnt).addClass("hidden");
        }
    };
    Basket.onDelete = function(idKey){
        Basket.onAdd(idKey);
    };
    $(".product-card-standart").off("click", ".add-to-cart");
    $(".product-card-standart").on("click", ".add-to-cart", function(e){
        e.stopPropagation();
        if ($(this).hasClass("in-cart")) Basket.open();
        else {
            $(".product-card-standart .product-form .spin-edit").eq(0).children(".spin-up").trigger("click");
        }
    });
    // select count by sizes
    $(".product-card-standart").on("click", ".product-form .spin-edit .spin-btn", function(e){
        e.stopPropagation();
        var $this  = $(this),
            plus  = $this.hasClass("spin-up"),
            minus = $this.hasClass("spin-down"),
            $input = $this.siblings("input[type=\"text\"]"),
            iVal   = parseInt($input.val())||0,
            idKey  = $input.data("idkey"),
            recalc = Basket.isSetKey(idKey);
        iVal = plus ? (iVal + 1) : (iVal - 1);
        if (iVal > 0) {
            Basket.add(idKey, iVal, recalc);
        } else {
            Basket.del(idKey);
        }
    });
});

function CAssortment(){
    var Assortment = {
            form: null,
            construct: function(){
                var self = this;
                self.form = $(".product-form");
            },
            setup: function(){
                var self = this;
                self.construct();
            }
        },
        ProductAssortment = {
            parent: null,
            sizes: null,
            construct: function(){
                var self = this;
                self.parent = window.Assortment;
                self.parent.construct();
            }
        },
        ProductPrintAssortment = {
            parent: null,
            form: null,
            hash: null,
            colors: null,
            labels: null,
            construct: function(){
                var self = this;
                self.form = $(".product-form");
                self.colors = self.form.children(".select-color").find("input[type=\"radio\"]");
                self.labels = self.form.children(".select-color").find(".check-label");
            },
            setup: function(){
                var self = this;
                self.construct();
                // check hash on load or select color
                $(window).on("load hashchange", function(){
                    self.checkHash();
                });
                // set hash on select color
                self.colors.on("change", function(){
                    if ($(this).is(":checked")) {
                        self.setHash($(this).data("color-hex"));
                        self.selectImage($(this).data("color-id"));
                    }
                });
                // change image on color hover
                self.labels.on("mouseenter", function(){
                    var $color = $(this).prev("input[type=\"radio\"]");
                    self.selectImage($color.data("color-id"));
                }).on("mouseleave", function(){
                    self.unSelectImage();
                });
            },
            checkHash: function(){
                var self = this;
                self.hash = window.location.hash;
                if (self.hash.length) {
                    self.selectColor(self.hash.substr(self.hash.indexOf("#") + 1));
                }
            },
            setHash: function(hex){
                window.location.hash = hex;
            },
            selectColor: function(hex){
                var self = this;
                $.map(self.colors, function(input){
                    var match = ($(input).data("color-hex")==hex);
                    $(input).prop("checked", match).change();
                });
            },
            selectImage: function(colorID){
                var self = this;
                if (!window.Gallery.instance.slides.length) return false;
                for (var i in window.Gallery.instance.slides) {
                    var slide = window.Gallery.instance.slides[i];
                    if ($(slide).data("color-id")==colorID) window.Gallery.instance.slideTo(i, 0);
                } return true;
            },
            unSelectImage: function(){
                var self = this;
                if (!window.Gallery.instance.slides.length) return false;
                $.map(self.colors, function(input){
                    if ($(input).is(":checked")) {
                        var colorID = $(input).data("color-id");
                        for (var i in window.Gallery.instance.slides) {
                            var slide = window.Gallery.instance.slides[i];
                            if ($(slide).data("color-id")==colorID) window.Gallery.instance.slideTo(i, 0);
                        }
                    }
                }); return true;
            }
        };
    this.init = function(){
        return Assortment;
    };
    this.init_product = function(){
        return ProductAssortment;
    };
    this.init_print = function(){
        return ProductPrintAssortment;
    };
};

function ProductRelated(){
    var Related = {
        nav: null,
        tabs: null,
        sliderParams: null,
        construct: function(){
            var self  = this;
            self.nav  = $(".product-related").children(".slider-nav");
            self.tabs = $(".product-related").children(".slider-tabs");
        },
        initialize: function(){
            var self  = this;
            self.construct();
            self.nav.on("click", "a", function(){
                $(this).siblings("a").removeClass("selected");
                $(this).addClass("selected");
                var idx = $(this).data("index");
                self.toggleTab(idx);
            });
            self.toggleTab(0);
        },
        toggleTab: function(idx){
            var self  = this,
                tab   = self.tabs.children(".slider-tab").eq(idx);
            tab.siblings(".slider-tab").removeClass("selected");
            tab.addClass("selected");
            var s = SwiperSlider.construct(tab.children(".slider"), {
                freeMode: true,
                autoHeight: true,
                slidesPerView: 5,
                preventClicks: false,
                preventClicksPropagation: false,
                spaceBetween: 2,
                breakpoints: {
                    1425: {
                      slidesPerView: "4"
                    },
                    1130: {
                      slidesPerView: "3"
                    },
                    960: {
                      slidesPerView: "auto"
                    }
                }
            });
        }
    };
    this.init = function(){
        return Related;
    };
};

function ProductGallery() {
    var ProductGallery = {
        thumbs: null,
        screen: null,
        screenParams: new Object(),
        thumbsParams: new Object(),
        zoom: null,
        switch: null,
        slider: null,
        slider2: null,
        slides: null,
        instance: null,
        instance2: null,
        construct: function(){
            var self = this;
            if($(".product-gallery").length>0) {
                self.switch = $(".product-gallery .switch");
                self.thumbs = $(".product-gallery .thumbs");
                self.screen = $(".product-gallery .screen");
                self.screenParams = {
                    direction: 'horizontal',
                    slidesPerView: 1,
                    autoHeight: false,
                    slideToClickedSlide: false
                };
                self.thumbsParams = {
                    direction: 'horizontal',
                    spaceBetween: 11,
                    slidesPerView: 3,
                    slideToClickedSlide: false,
                    autoHeight: true
                };
                self.slider = self.screen.children(".swiper-container");
                self.slider2 = self.thumbs.children(".swiper-container");
                self.slides = self.thumbs.find(".swiper-slide");
                self.zoom = self.screen.find(".zoom-img").elevateZoom({
                    zoomWindowOffety: -13,
                    zoomWindowOffetx: 80,
                    zoomWindowWidth: 735,
                    zoomWindowHeight: 591
                });
            }
        },
        initialize: function(){
            var self = this;
            self.construct();
            self.instance = new Swiper(self.slider, self.screenParams);
            self.instance2 = new Swiper(self.slider2, self.thumbsParams);//self.slider2.swiper(self.thumbsParams);
            self.slides.on("click", function (e) {
                var idx = $(this).index();
                $(this).siblings(".swiper-slide").removeClass("selected");
                $(this).addClass("selected");
                self.instance.slideTo(idx);
                self.instance2.slideTo(idx);
            });
            self.instance.on('slideChange', function(){
                var idx = this.activeIndex;
                self.slides.eq(idx).trigger("click");
                $.map(self.switch.find("a"), function(a){
                    var i = $(a).data("index");
                    if (i==idx) $(a).addClass("selected");
                    else $(a).removeClass("selected");
                });
            });
            self.switch.on("click", "a", function(){
                var idx = $(this).data("index");
                self.instance.slideTo(idx);
            });
        }
    };
    this.init = function () {
        return ProductGallery;
    };
};
