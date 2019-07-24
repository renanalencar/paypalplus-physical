<?php
require_once("functions.php");
session_start();
$access_token = $_SESSION['access_token'];
$paypalMode = $_GET['paypalMode'];
$payerId = $_GET['payerId'];
$paymentID = $_GET['paymentID'];

	if ($paypalMode=="sandbox") {
    	$host = 'https://api.sandbox.paypal.com';
	}
	if ($paypalMode=="live") {
   		$host = 'https://api.paypal.com';
	}
#GET ACCESS TOKEN
$url = $host.'/v1/payments/payment/'.$paymentID.'/execute/'; 
$execute = '{"payer_id" : "'.$payerId.'"}';
$json_resp = make_post_call($url, $execute);
$json_respf = stripslashes(json_format($json_resp));
$event_json = json_decode(json_encode($json_resp), FALSE);
$sale_id = $event_json->transactions[0]->related_resources[0]->sale->id;

?>
<html>
<head>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Checkout</title>

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

<body>
<div>
    <div class="col-md-12">
    <h1>Seu pagamento foi processado. Obrigado!
    </h1>
    <br>
    <h2><a href="index.html">Voltar</a>
    </h2>
    <br>
    <h3>ID de transação
    </h3>
    <pre class="json-data"><?php echo $sale_id;?></pre>

    </div>
</div>

</body>
</html>