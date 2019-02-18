window.addEventListener("load", FeedbackFormInit, false);

function FeedbackFormInit(){
    if (typeof window.grecaptcha != "undefined") {
        var $form = $("#FeedbackForm"),
            $target = $form.closest(".contact-wrapper"),
            captcha = grecaptcha.render('g_recaptcha', {
                callback : FeedbackFormVerify
            });
        $form.ajaxForm({
            target: $target,
            success: function(){
                FeedbackFormInit();
            }
        });
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
    } else {
        setTimeout(FeedbackFormInit, 100);
    }
};

function FeedbackFormVerify(){
    $("#FeedbackForm").find(".btn-primary").prop("disabled", false);
};