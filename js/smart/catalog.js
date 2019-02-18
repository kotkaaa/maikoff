/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var objFilters = new CFilters(),
    CategoryFilters = objFilters.init(),
    Filters = objFilters.init_filters(),
    MobileFilters = objFilters.init_mobile();

$(function(){
    // init filters
    Filters.initialize();
    // init mobile filters
    MobileFilters.initialize();
    CategoryFilters.initFilterActions('#filters_form');
    CategoryFilters.initFilterActions('#filters_form_mobile');
    CategoryFilters.initFilterActions('#filters_subcategories');
    CategoryFilters.initUpdatePage(100);
    // init pager actions
    $("#pager").on("click", ".btn-load-more", function(e){
        e.preventDefault();
        var url = $(this).data("href");
        AjaxLoadMore(url);
    });
});

function CFilters(){
    var CategoryFilters = {
        initFilterActions: function(selector){
            var self = this;
            $(selector).on('click', '.filter-element', function(e) {
                e.preventDefault();
                var link = $(this).hasClass('filter-element') ? $(this) : $(this).find('.filter-element'),
                    url = ((typeof $(link).attr('data-href') != "undefined") ? $(link).attr('data-href') : $(link).attr('href')),
                    disabled = link.hasClass("disabled");
                if (!disabled) self.forceUpdatePage(url);
            });
        },
        initUpdatePage: function(timeout){
            var self = this;
            if (typeof jQuery != "undefined" && typeof window.History != "undefined") {
                $(window).on("statechange", function (){
                    self.AjaxUpdatePage(window.location.href);
                });
            } else {
                setTimeout(function(){
                    self.initUpdatePage(timeout);
                }, timeout);
            }
        },
        forceUpdatePage: function(url){
            var self = this;
            if (History.enabled) {
                History.pushState(null, document.title, url);
            } else {
                self.AjaxUpdatePage(url);
            }
        },
        AjaxUpdatePage: function(url) {
            var self      = this,
                Filters   = $('#filters_form'),
                MFilters  = $('#filters_form_mobile'),
                Selected  = $("#selected_filters"),
                Subcats   = $("#filters_subcategories"),
                Toolbar   = $("#mToolbar"),
                Products  = $("#products"),
                Pager     = $("#pager"),
                HTitle    = $(".page-heading"),
                CtrlView  = $("#control_view"),
                SText     = $(".content.seo"),
                Breadcrumbs = $(".breadcrumbs"),
                STitle    = document.getElementsByTagName("title")[0],
                MDescr    = document.getElementsByName("description")[0],
                MKey      = document.getElementsByName("keywords")[0];
            $.ajax({
                url:  url,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajaxUpdate: 1
                },
                beforeSend: function(){
                    Filters.addClass("load");
                    MFilters.addClass("load");
                    Products.addClass("load");
                },
                complete: function(response){
                    Filters.removeClass("load");
                    MFilters.removeClass("load");
                    Products.removeClass("load");
                },
                success: function(json){
                    if(json) {
                        if (json.redirect_url) window.location.assign(json.redirect_url);
                        if (json.filters) Filters.html(json.filters);
                        if (json.filters_mobile) MFilters.html(json.filters_mobile);
                        if (json.subcategories) Subcats.html(json.subcategories);
                        if (typeof json.selected_filters != "undefined") Selected.html(json.selected_filters);
                        if (json.total_pages && parseInt(json.total_pages)>1) Pager.removeClass("hidden");
                        else Pager.addClass("hidden");
                        if (json.pager) {
                            Pager.html(json.pager);
                        }
                        if (json.selected_count) {
                            var cnt = parseInt(json.selected_count);
                            Toolbar.find(".selected-count").text(cnt > 0 ? "+" + cnt : "");
                        } else Toolbar.find(".selected-count").text("");
                        if (json.breadcrumbs && Breadcrumbs.length) Breadcrumbs.html(json.breadcrumbs);
                        if (json.products && Products.length) Products.html(json.products);
                        if (typeof json.h_title!="undefined" && HTitle.length) HTitle.text(json.h_title);
                        if (typeof json.seo_text!="undefined" && SText.length) SText.html(json.seo_text);
                        if (typeof json.meta_descr!="undefined") $(MDescr).attr("content", json.meta_descr);
                        if (typeof json.meta_key!="undefined") $(MKey).attr("content", json.meta_key);
                        if (typeof json.seo_title!="undefined") {
                            STitle.innerHtml = json.seo_title;
                            document.title = json.seo_title;
                        }
                        var metaRobots = $('#sMetaRobots');
                        if (typeof json.meta_robots!="undefined") {
                            var metaTag = '<meta id="sMetaRobots" name="robots" content="'+json.meta_robots+'">';
                            if (metaRobots.length) {
                                $(metaRobots).replaceWith(metaTag);
                            } else {
                                $('head').append(metaTag);
                            }
                        } else if (metaRobots){
                            metaRobots.remove();
                        }
                        //search
                        if (json.search_result) {
                            $('.search_result').html(json.search_result);
                        }
                    }
                    self.initFilterActions('#selected_filters');
                    self.initFilterActions('#control_view');
                    self.initFilterActions('#filters_subcategories');
                    window.Filters.initialize();
                    window.MobileFilters.initialize();
                }
            }); return false;
        }
    },
    Filters = {
        form: null,
        catalog: null,
        sections: null,
        construct: function(){
            var self = this;
            self.form = $("#filters_form");
            self.catalog = self.form.children(".catalog");
            self.sections = self.form.children(".section");
        },
        initialize: function(){
            var self = this;
            self.construct();
            // enable toggle sections
            self.form.find(".section-toggle").on("click", function(e){
                e.preventDefault();
                var el = $(this),
                    section = el.closest(".section");
                el.toggleClass("on");
                section.toggleClass("collapsed");
            });
            // open-close catalog
            self.form.on("mouseenter", ".catalog", function(){
                $(this).stop("br").addClass("hover");
                $(this).delay(400).queue(function(cs){
                    if ($(this).hasClass("hover")) {
                        $(this).addClass("open");
                    } cs();
                });
            }).on("mouseleave", ".catalog", function(){
                $(this).removeClass("hover");
                $(this).delay(400).queue(function(br){
                    if (!$(this).hasClass("hover")) {
                        $(this).removeClass("open");
                    } br();
                });
            }).on("click", ".catalog .filter-shift", function(e){
                e.preventDefault();
                var root = self.form.find(".catalog .root"),
                    sub  = $(this).next(".sublevel");
                root.addClass("shift");
                root.find(".sublevel").removeClass("shift");
                sub.addClass("shift");
            }).on("click", ".catalog .return", function(e){
                e.preventDefault();
                var root = self.form.find(".catalog .root"),
                    sub  = $(this).closest(".sublevel");
                root.removeClass("shift");
                sub.removeClass("shift");
            }).on("click", ".close", function(e){
                e.preventDefault();
                self.form.find(".catalog").stop().removeClass("hover").removeClass("open");
            });
        }
    },
    MobileFilters = {
        active: false,
        toolbar: null,
        filters_trigger: null,
        sorting_trigger: null,
        body: null,
        root: null,
        form: null,
        sections: null,
        construct: function () {
            var self = this;
            self.body = $(".page-body");
            self.root = $(".filters-popup");
            self.form = self.root.children(".filters-form");
            self.sections = self.form.children(".section");
            self.toolbar = $(".toolbar");
            self.filters_trigger = self.toolbar.find("[data-toggle=\"filters\"]");
            self.sorting_trigger = self.toolbar.find("[data-toggle=\"sorting\"]");
        },
        initialize: function () {
            var self = this;
            self.construct();
            self.root.on("click", function (e) {
                e.stopPropagation();
            });
            self.toolbar.on("click", "[data-toggle=\"filters\"]", function (e) {
                e.stopPropagation();
                self.open();
            }).on("click", "[data-toggle=\"sorting\"], [data-toggle=\"category\"]", function (e) {
                e.stopPropagation();
                self.open();
                var section = self.form.children("[data-section=\"" + $(this).data("toggle") + "\"]");
                if (section.length)
                    self.openSection(section);
            });
            self.sections.on("click", ".section-toggle", function (e) {
                e.stopPropagation();
                self.open();
                var section = $(this).closest(".section");
                if (section.length)
                    self.toggleSection(section);
            });
            self.sections.find(".level-more").children("a").on("click", function(e){
                e.preventDefault();
                var el = $(this),
                    section = el.closest(".section"),
                    li = el.closest(".level-more"),
                    list = section.find(".list");
                li.addClass("opened");
                list.addClass("shift");
                section.children(".section-wrap").scrollTop(0);
            });
            self.sections.find(".return").on("click", function(e){
                e.preventDefault();
                var el = $(this),
                    section = el.closest(".section"),
                    li = el.closest(".level-more"),
                    list = section.find(".list");
                li.removeClass("opened");
                list.removeClass("shift");
            });
            self.body.on("click", function (e) {
                if (self.active) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggle();
                }
            });
            self.root.swipe({
                swipeLeft: function (event, direction, distance, duration, fingerCount, fingerData, currentDirection) {
                    event.preventDefault();
                    if (distance >= 50)
                        self.toggle();
                    console.log("You swiped " + direction);
                }
            });
        },
        toggle: function () {
            var self = this;
            if (self.active) self.close();
            else self.open();
        },
        open: function () {
            var self = this;
            self.beforeOpen();
            self.root.addClass("shift");
            self.body.addClass("turn-left");
            $("html,body").addClass("noswipe");
            self.active = true;
        },
        beforeOpen: function () {
            var self = this;
            // Remove page turn class
            self.body.removeClass("turn-down").removeClass("turn-left").removeClass("turn-right").removeClass("fixed").removeClass("noswipe");
            // Deactivate callback block
            $(".header-container").find(".drop-down-phone").removeClass("show");
            $(".header-container").find(".btn-mob-phone").removeClass("active");
            $(".header-container").find(".drop-down-recall").addClass("hidden");
            // Deactivate search block
            $(".header-container").find(".search").removeClass("show");
            $(".header-container").find(".btn-search-mob").removeClass("active");
            $(".header-container").find(".center-section").removeClass("height");
        },
        close: function () {
            var self = this;
            self.root.removeClass("shift");
            self.body.removeClass("turn-left");
            $("html,body").removeClass("noswipe");
            $.map(self.sections, function (section) {
                self.closeSection(section);
            });
            self.active = false;
        },
        openSection: function (section) {
            var self = this;
            $(section).addClass("expanded");
            var wrap = $(section).children(".section-wrap");
            $(wrap).perfectScrollbar();
        },
        closeSection: function (section) {
            var self = this;
            $(section).removeClass("expanded");
        },
        toggleSection: function (section) {
            var self = this;
            if ($(section).hasClass("expanded"))
                self.closeSection(section);
            else
                self.openSection(section);
        },
        apply: function () {
            var self = this;
            self.close()
        },
        cancel: function () {
            var self = this;
            self.close()
        }
    };
    this.init = function () {
        return CategoryFilters;
    };
    this.init_filters = function(){
        return Filters;
    };
    this.init_mobile = function () {
        return MobileFilters;
    };
};

function AjaxLoadMore(url) {
    var Products  = $("#products"),
        Pager     = $("#pager");
    $.ajax({
        url:  url,
        type: 'POST',
        dataType: 'json',
        data: {
            ajaxLoadMore: 1
        },
        success: function (json) {
            if (json) {
                if (json.products) {
                    Products.find(".product-next-page").remove();
                    Products.append(json.products);
                }
                if (json.pager)    Pager.html(json.pager);
            }
        }
    }); return false;
};