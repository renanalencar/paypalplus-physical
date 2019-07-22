<?php

require_once("functions.php");
header('content-type: text/html; charset: utf-8');

# Get form data
$itemName=$_REQUEST['itemName'];
$itemDescription=$_REQUEST['itemDescription'];
$itemSku=$_REQUEST['itemSku'];
$itemPrice=$_REQUEST['itemPrice'];
$itemQuantity=$_REQUEST['itemQuantity'];
$payerEmail=$_REQUEST['payerEmail'];
$payerPhone=$_REQUEST['payerPhone'];
$payerFirstName=$_REQUEST['payerFirstName'];
$payerLastName=$_REQUEST['payerLastName'];
$shippingAddressStreet1=$_REQUEST['shippingAddressStreet1'];
$shippingAddressStreet2=$_REQUEST['shippingAddressStreet2'];
$shippingAddressPostal=$_REQUEST['shippingAddressPostal'];
$shippingAddressCity=$_REQUEST['shippingAddressCity'];
$shippingAddressCountry=$_REQUEST['shippingAddressCountry'];
$shippingAddressState=$_REQUEST['shippingAddressState'];
$disallowRememberedCards='true';
$rememberedCards='';
$paypalMode='sandbox';
$clientId= 'ATq8WIoO-jT0S2njs1a6VdStorN12BydaApeZ6W5ZJ6k3cA0eAnt_ps1mrlB66YTVKoHyDhqDioJpX3e';
$secret='EEKVUBKnYLh15fux7e0RgGE2jQa9Dg81G5T-BGqzbLXyc8P-njfnIELpKT_slxhQIuDth8jYe9l6RKZ2';
$returnUrl='http://noviamiaphotography.com/plus7/ppp.php';
$cancelUrl='http://noviamiaphotography.com/plus7/ppp.php';
$ppplusJsLibraryLang='pt_BR';
$currency=$_REQUEST['currency'];
$iframeHeight='';
$merchantInstallmentSelection='1';
$merchantInstallmentSelectionOptional='true';

$total = number_format($itemPrice * $itemQuantity,2);

if ($paypalMode=="sandbox") {
    $host = 'https://api.sandbox.paypal.com';
}
if ($paypalMode=="live") {
    $host = 'https://api.paypal.com';
}
#GET ACCESS TOKEN

$url = $host.'/v1/oauth2/token'; 
$postArgs = 'grant_type=client_credentials';
$access_token= get_access_token($url,$postArgs);


#CREATE PAYMENT
$url = $host.'/v1/payments/payment';
$payment = '{
  "intent": "sale",
   "application_context": {
        "shipping_preference": "SET_PROVIDED_ADDRESS"
    },
  "payer": {
    "payment_method": "paypal"
  },
  "transactions": [
    {
        "amount": {
        "currency": "'.$currency.'",
        "total": "'.$total.'",
        "details": {}
      },
      "description": "Payment through PayPal Plus",
      "custom": "'.$itemDescription.'",
      "payment_options": {
        "allowed_payment_method": "IMMEDIATE_PAY"
      },
      "item_list": {
        "items": [
          {
            "name": "'.$itemName.'",
            "description": "'.$itemDescription.'",
            "quantity": "'.$itemQuantity.'",
            "price": "'.$itemPrice.'",
            "sku": "'.$itemSku.'",
            "currency": "'.$currency.'"
          }
        ],
         "shipping_address": {
          "recipient_name": "'.$payerFirstName.'",
          "line1": "'.$shippingAddressStreet1.'",
          "line2": "'.$shippingAddressStreet2.'",
          "city": "'.$shippingAddressCity.'",
          "country_code": "'.$shippingAddressCountry.'",
          "postal_code": "'.$shippingAddressPostal.'",
          "state": "'.$shippingAddressState.'",
          "phone": "'.$payerPhone.'"
        }
      }
    }
  ],
  "redirect_urls": {
    "return_url": "'.$returnUrl.'",
    "cancel_url": "'.$cancelUrl.'"
  }
}
';

//var_dump ($json);
//die($payment);
$json_resp = make_post_call($url, $payment);

#Get the approval URL for later use
$approval_url = $json_resp['links']['1']['href'];

#Get the token out of the approval URL
$token = substr($approval_url,-20);

#Get the PaymentID for later use
$paymentID = ($json_resp['id']);

#Put JSON in a nice readable format
$json_resp = stripslashes(json_format($json_resp));


?>
<html>
<head>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Caixa</title>

    <link rel="stylesheet" type="text/css"
        href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css"
        href="./Shop_files/shop.css" />

    <style type="text/css">

        #loader-layer {
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            position: absolute;
            padding:0;
            margin:0;
            top:0;
            left:0;
            z-index: 100;
            display: none;
        }
        
        /* Center the loader */
        #loader {
          position: absolute;
          left: 50%;
          top: 50%;
          z-index: 1;
          width: 150px;
          height: 150px;
          margin: -75px 0 0 -75px;
          border: 16px solid #f3f3f3;
          border-radius: 50%;
          border-top: 16px solid #3498db;
          width: 120px;
          height: 120px;
          -webkit-animation: spin 2s linear infinite;
          animation: spin 2s linear infinite;
          display: none;
        }

        @-webkit-keyframes spin {
          0% { -webkit-transform: rotate(0deg); }
          100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

    </style>
</head>
<body id="debug">
    <div id="loader-layer"></div>
    <div id="loader"></div>
    <div style="display: none;" id="paypal-config"
        data-checkout="inline"
        data-checkout-url="http://paypalplussampleshopbr-sandbox-9451.ccg21.dev.paypalcorp.com/PayPalPlusSampleShop-br/checkout-now"
    ></div>

    <div class="container">

    <h1 class="page-header">Pagamentos</h1>

        <div class="row" style="">

            <form method="post" class="horizontal-form" action="?action=inline"
                id="checkout-form" onSubmit="return false;"
                data-checkout="inline">

            <div class="col-md-6">

                <div class="form-group" id="psp-group">

                    <div class="panel">
                        <div class="panel-body">
                            <div id="pppDiv"> <!-- the div which id the merchant reaches into the clientlib configuration -->
                                <script type="text/javascript">document.write("iframe is loading...");</script>
                                <noscript> <!-- in case the shop works without javascript and the user has really disabled it and gets to the merchant's checkout page -->
                                    <iframe src="https://www.paypalobjects.com/webstatic/ppplusbr/ppplusbr.min.js/public/pages/pt_BR/nojserror.html" style="height: 400px; border: none;"></iframe>
                                </noscript>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <h2>Detalhes</h2>

                <div class="form-group" id="shipping-address-group">
                    <table class="table table-striped">
                        <tr>
                            <td>Produto</td>
                            <td>Quantidade</td>
                            <td>Preço</td>
                        </tr>

                            <tr>
                                <td><?php echo $itemName?></td>
                                <td><?php echo $itemQuantity?></td>
                                <td>$<?php echo $itemPrice." ".  $currency?></td>
                            </tr>
                    </table>
                    <label class="control-label">Total: <?php echo $total." ".  $currency?></label>
                </div>

                <div class="form-group" id="shipping-address-group">
                    <label class="control-label">Dados do cliente</label>
                    <table class="table table-striped">
                        <tr>
                            <td>Nome:</td>
                            <td><?php echo $payerFirstName ." " . $payerLastName ?></td>
                        </tr>
                        <tr>
                            <td>Endereço:</td>
                            <td><?php echo $shippingAddressStreet1 ?></td>
                        </tr>
                        <tr>
                            <td>Complemento:</td>
                            <td><?php echo $shippingAddressStreet2 ?></td>
                        </tr>
                        <tr>
                            <td>CEP:</td>
                            <td><?php echo $shippingAddressPostal ?></td>
                        </tr>
                        <tr>
                            <td>Cidade:</td>
                            <td><?php echo $shippingAddressCity ?></td>
                        </tr>
                        <tr>
                            <td>Estado:</td>
                            <td><?php echo $shippingAddressState ?></td>
                        </tr>
                        <tr>
                            <td>País:</td>
                            <td><?php echo $shippingAddressCountry ?></td>
                        </tr>
                    </table>
                </div>
                <br/>
                <p><strong>Clique para continuar:</strong></p>
                <button
                  type="submit"
                  id="continueButton"
                  class="btn btn-lg btn-primary btn-block infamous-continue-button"
                  onclick="ppp.doContinue(); return false;">
                    
                    Continuar
                </button>
            </div><!-- col -->
            </form>
        </div><!-- row -->
    </div><!-- container -->

<script src="https://www.paypalobjects.com/webstatic/ppplusdcc/ppplusdcc.min.js?ver=3.1.2"></script>
<script>

    var ppp = PAYPAL.apps.PPP({

        approvalUrl: "<?php echo $approval_url;?>",

        buttonLocation: "outside",
        preselection: "none",
        surcharging: false,
        hideAmount: false,
        placeholder: "pppDiv",

        disableContinue: "continueButton",
        enableContinue: "continueButton",

        // merchant integration note:
        // this is executed when the iframe posts the "checkout" action to the library
        // the merchant can do an ajax call to his shop backend to save the remembered cards token
        onContinue: function (rememberedCards, payerId, token, term) {
            console.log(term);
            $('#continueButton').addClass('hidden');
            var paymentID = "<?php echo $paymentID; ?>";
            var paypalMode = "<?php echo $paypalMode; ?>";
            var payURL = "ExecutePayment.php?payerId=" + payerId + "&paymentID=" + paymentID + "&paypalMode=" + paypalMode;
            document.getElementById("loader-layer").style.display = "block";
            document.getElementById("loader").style.display = "block";
            window.top.location = payURL;

            document.getElementById("installmentsJson").innerHTML = (term ? "<p><strong><code id='installmentsText'>"+ JSON.stringify(term) +"</code></strong></p>" : "No installments option selected");
           
		    document.getElementById("responseJson").innerHTML = JSON.stringify('Success');            
        },

        onError: function (err) {
            var msg = jQuery("#responseOnError").html()  + "<BR />" + JSON.stringify(err);
            jQuery("#responseOnError").html(msg);
        },

        language: "<?php echo $ppplusJsLibraryLang; ?>",
        country: "<?php echo $shippingAddressCountry; ?>",
        disallowRememberedCards: "<?php echo $disallowRememberedCards; ?>",
        rememberedCards: "<?php echo $rememberedCards; ?>",
        mode: "<?php echo $paypalMode; ?>",
        useraction: "continue",
        payerEmail: "<?php echo $payerEmail; ?>",
        payerPhone: "<?php echo $payerPhone; ?>",
        payerFirstName: "<?php echo $payerFirstName; ?>",
        payerLastName: "<?php echo $payerLastName; ?>",
        payerTaxId: "",
        payerTaxIdType: "",
        merchantInstallmentSelection: "<?php echo $merchantInstallmentSelection; ?>",
        merchantInstallmentSelectionOptional:"<?php echo $merchantInstallmentSelectionOptional; ?>",
        hideMxDebitCards: false,
        iframeHeight: "<?php echo $iframeHeight; ?>"
        
    });
</script>

<style>
    .hidden {display:none;}
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>
$("#debug").on("click", function (e) {
    $('.open').toggleClass('open closed');
});
$("#sessionInfo").on("click", function (e) {
    e.stopPropagation();
});
$("#sessionInfoDrawer").on("click", function (e) {
    e.stopPropagation();
    $(this).toggleClass('closed open');
});
</script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="./Shop_files/sample_shop.js"></script>

</body>
</html>

