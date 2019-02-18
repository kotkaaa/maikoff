var objCheckout = new CCheckout(),
    Checkout = objCheckout.init();

$(function(){
    Checkout.setup();
});

function CCheckout(){
    var Checkout = {
        form: null,
        fields: null,
        fields: null,
        storage: new Object(),
        construct: function(){
            var self = this;
            self.form = $("#checkoutForm");
            self.fields = self.form.find("input,select,textarea");
            var formData = window.localStorage.getItem("checkoutFormData");
            if (formData !== null) self.storage = JSON.parse(formData);
        },
        destruct: function() {
            var self = this;
            window.localStorage.setItem("checkoutFormData", JSON.stringify(self.storage));
            return true;
        },
        setup: function(){
            var self = this;
            self.construct();
            // add input phone mask
            self.form.find("input[type=\"tel\"]").inputmask({
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
            // add validate event for all fields
            self.form.on("change blur", ".requiredfield", function(){
                var iVal  = $(this).val(),
                    iName = $(this).attr("name");
                // Set basket shipping type by selected shipping type
                if (iName == "shipping_id") self.setShipping(iVal);
                if (iName == "payment_id") self.setPayment(iVal);
                // Fill recepient name if empty
                if (iName == "name") self.setRecepient(iVal);
                // validate input
                // self.checkField(this);
            }).on("submit", function(){
                return self.validate();
            });
            // cities and warehouses autocomplete
            self.form.find("#city").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: self.form.attr("action"),
                        type: 'GET',
                        dataType: "json",
                        data: {
                            raw: request.term,
                            action: "getCities"
                        },
                        success: function (data) {
                            console.log(data);
                            response($.map(data.items, function(item){
                                return {
                                    value: item.DescriptionRu, // наименование товара
                                    label: item.DescriptionRu // наименование товара
                                }
                            }));
                        }
                    });
                },
                minLength: 3,
                delay: 300,
            });
            self.form.find("#address").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: self.form.attr("action"),
                        type: 'GET',
                        dataType: "json",
                        data: {
                            raw: request.term,
                            city: self.form.find("#city").val(),
                            action: "getWarehouses"
                        },
                        success: function (data) {
                            response($.map(data.items, function(item){
                                return {
                                    value: item.DescriptionRu, // ссылка на страницу товара
                                    label: item.DescriptionRu // наименование товара
                                }
                            }));
                        }
                    });
                },
                minLength: 3,
                delay: 300,
            });
            // fill local storage from form data
            $(window).on("unload", function(){
                $.map(self.fields, function(input){
                    var iVal  = $(input).val(),
                        iName = $(input).attr("name");
                    self.storage[iName] = iVal;
                }); return self.destruct();
            });
            // fill up form for local storage data
            self.fillup();
        },
        setRecepient: function(name) {
            var self = this,
                recepient = self.form.find("#recepient");
            if (!recepient.val().length && name.length) recepient.val(name).change();
        },
        setShipping: function(shippingID){
            var self = this,
                divs = self.form.find(".toggle-content");
            $.map(divs, function(el){
                var lname = el.localName;
                if ($(el).data("shipping-id") == shippingID) {
                    $(el).removeClass("hidden");
                    if (lname=="input" || lname=="textarea" || lname=="select") $(el).prop("disabled", false);
                } else {
                    $(el).addClass("hidden");
                    if (lname=="input" || lname=="textarea" || lname=="select") $(el).prop("disabled", true);
                }
            }); Basket.setShipping(shippingID);
        },
        setPayment: function(paymentID){
            var self = this,
                divs = self.form.find(".toggle-content");
            $.map(divs, function(el){
                var lname = el.localName;
                if ($(el).data("payment-id") == paymentID) {
                    $(el).removeClass("hidden");
                    if (lname=="input" || lname=="textarea" || lname=="select") $(el).prop("disabled", false);
                } else {
                    $(el).addClass("hidden");
                    if (lname=="input" || lname=="textarea" || lname=="select") $(el).prop("disabled", true);
                }
            }); Basket.setPayment(paymentID);
        },
        fillup: function(){
            var self = this;
            $.map(self.fields, function(field) {
                var name = field.name;
                if (name && array_key_exists(name, self.storage)) {
                    $(field).val(self.storage[name]).trigger("change");
                    if (name == "comment" && !empty($(field).val())) {
                        $(field).prev(".toggle").addClass("toggle-on");
                    }
                }
            });
        },
        getCities: function(raw){
            var self = this;

        },
        getWarehouses: function(raw){
            var self = this;
        },
        checkField: function(input){
            var self = this,
                iName = $(input).attr("name"),
                iVal  = $(input).val(),
                regExEmail = /^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$/,
                regExPhone = /^\+38\s(044|039|050|063|066|067|068|091|092|093|094|095|096|097|098|099|073)+([\d\s\-]{10})$/,
                errors = 0;
            $(input).removeClass("error");
            if (iVal.length==0 /*|| (iName=="email" && iVal.match(regExEmail)==null)*/ || (iName=="phone" && iVal.match(regExPhone)==null)) {
                $(input).addClass("error");
                errors++;
            }; return errors;
        },
        validate: function(){
            var self   = this,
                btn    = self.form.find(".btn-warning"),
                errors = 0;
            $.map(self.form.find(".requiredfield:not(.hidden)"), function(input){
                errors += self.checkField(input);
            }); errors = Boolean(errors);
//            btn.prop("disabled", errors);
            return !errors;
        }
    };
    this.init = function(){
        return Checkout;
    };
};