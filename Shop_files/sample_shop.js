var PAYPAL = PAYPAL || {};

PAYPAL.apps = PAYPAL.apps || {};

$(function() {
    "use strict";

    // JSON prettify

    $("pre.json-data").each(function(index) {
        var text = $(this).text();
        var json = JSON.parse(text);
        var pretty = JSON.stringify(json, undefined, 2);
        $(this).text(pretty);
    });


    // debug link

    var body = $("body");

    $("#debugLink").on("click", function(e) {
        body.toggleClass("debug");
        // XXX: What iframe is being meant here?!
        iframe.location = iframe.location + "#debug"
    });

    // Sample Shop Object

    PAYPAL.apps.PPPSampleShop = (function() {

        var billingAddressField = $("#billing-address");
        var billingAddressGroup = $("#billing-address-group");
        var shippingAddressField = $("#shipping-address");
        var shippingAddressGroup = $("#shipping-address-group");

        var pspForm  = $("#checkout-form");
        var checkoutUrl = $("#paypal-config").attr("data-checkout-url");

        var PPP = PAYPAL.apps.PPP;

        function checkoutNow() {
            var pm = PPP.getPaymentMethod();
            if (pm.indexOf("3rd-") === 0) {
                window.location = "?action=" + pm;
            } else {
                window.location = checkoutUrl + "?paymentMethod=" + PPP.getPaymentMethod();
            }
        }

        function checkForm() {

            var errors = 0;
            $(".form-group").removeClass("has-error");

            if (billingAddressField.val().length <= 0) {
                billingAddressGroup.addClass("has-error");
                errors++;
            }
            if (shippingAddressField.val().length <= 0) {
                shippingAddressGroup.addClass("has-error");
                errors++;
            }

            if (errors !== 0) {
                return false;
            }
            if (pspForm.attr("data-checkout") === "immediate") {
                checkoutNow();
                return false;
            }
        }

        var getPaymentMethod = null;

        if ( typeof PPP === 'function' ) {
            getPaymentMethod = PPP.getPaymentMethod;
        }

        var baseUrls = null;
        if (PAYPAL.apps.PPPSampleShop) {
            baseUrls = PAYPAL.apps.PPPSampleShop.baseUrls;
        }

        return {
            checkForm: checkForm,
            getPaymentMethod: getPaymentMethod,
            checkoutNow: checkoutNow,
            baseUrls: baseUrls
        };

    })();
});

