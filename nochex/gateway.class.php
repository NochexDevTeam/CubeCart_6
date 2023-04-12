<?php

class Gateway {

	private $_config;

	private $_module;

	private $_basket;

	public function __construct($module = false, $basket = false) {

		$this->_config	=& $GLOBALS['config'];

		$this->_session	=& $GLOBALS['user'];

		$this->_module	= $module;

		$this->_basket =& $GLOBALS['cart']->basket;

	}

	##################################################

	public function transfer() {

		$transfer = array(

			'action' => 'https://secure.nochex.com/default.aspx',

			'method' => 'post',

			'target' => '_self',

			'submit' => 'auto',

		);

		return $transfer;

	}

	public function repeatVariables() {

		return false;
	}


public function fixedVariables() {		

/* Callback - Check if this option is enabled in the module */
$callback = "Enabled";

/* Test Mode - Check if this option is enabled in the module */
if($this->_module['testMode'] == "1"){
	$test_trans = "100";
}else{
	$test_trans = "";
}

/* Hide Billing Details - Check if this option is enabled in the module */
if($this->_module['hideMode'] == "1"){
	$hideBilling = "true";
}else{
	$hideBilling = "";
}

/* Xml Collection - Check if this option is enabled in the module */

if($this->_module['xmlMode'] == "1"){

	$itemCollect = "<items>";

	foreach ($this->_basket['contents'] as $key => $product) {

if($product['sale_price'] == 0){
$priceTot = number_format($product['price'], 2);
}else{
$priceTot = number_format($product['sale_price'], 2);
}

	$itemCollect .= "<item><id></id><name>" . $product['name'] . "</name><description>" . $product['description'] . "</description><quantity>" . $product['quantity'] . "</quantity><price>" . $priceTot . "</price></item>" ;	
	
	}

$itemCollect .="</items>";

$description = "Order created for: ". $this->_basket['cart_order_id'];

}else{

	$itemCollect = "";
	$description = "";	

	foreach ($this->_basket['contents'] as $key => $product) {

if($product['sale_price'] == 0){
	$priceTot = number_format($product['price'],2);
}else{
	$priceTot = number_format($product['sale_price'],2);
}
		
	$description .= "Product Name: " . $product['name'] . ", Product Description: " . $product['description'] . ", Product Quantity: " . $product['quantity'] . ", Product Price: " . $priceTot ;	
 
	} 
}

 
/* Postage - Check if this option is enabled in the module */

if($this->_module['postageMode'] == "1"){
 
$amountTot = $this->_basket['total'] - $this->_basket['shipping']['value'];

$postage= $this->_basket['shipping']['value'];
 
}else{
 
$amountTot = $this->_basket['total'];

$postage = 0;
 
}
 
/* retrieve billing & delivery addresses */
 
		$billing_address = array();

		if (!empty($this->_basket['billing_address']['line1']))	$billing_address[] = $this->_basket['billing_address']['line1'];

		if (!empty($this->_basket['billing_address']['line2']))	$billing_address[] = $this->_basket['billing_address']['line2'];

		$delivery_address = array();

		if (!empty($this->_basket['delivery_address']['line1'])) $delivery_address[] = $this->_basket['delivery_address']['line1'];

		if (!empty($this->_basket['delivery_address']['line2'])) $delivery_address[] = $this->_basket['delivery_address']['line2'];

/* Put all the details into an array to post customer to Nochex*/ 
if($GLOBALS['_COOKIE']["currency"] == "USD") {
	$merchantID = $this->_module['emailUSD'];
} else if ($GLOBALS['_COOKIE']["currency"] == "EUR") {
	$merchantID = $this->_module['emailEUR'];
} else {
	$merchantID = $this->_module['email'];
}

		$hidden = array(

			'merchant_id' => $merchantID,

			'amount' => number_format($amountTot,2),

			'postage' => number_format($postage,2),

			'description' => $description,

			'xml_item_collection' => $itemCollect,

			'hide_billing_details' => $hideBilling,

			'order_id' => $this->_basket['cart_order_id'],

			'optional_1' => $callback,

			'customer_phone_number' => $this->_basket['billing_address']['phone'],

			'billing_fullname' => $this->_basket['billing_address']['first_name'].' '.$this->_basket['billing_address']['last_name'],

			'billing_address' => implode("\r\n", $billing_address),

			'billing_city' => $this->_basket['billing_address']['town'],

			'billing_postcode' => $this->_basket['billing_address']['postcode'],

			'delivery_fullname' => $this->_basket['delivery_address']['first_name'].' '.$this->_basket['delivery_address']['last_name'],

			'delivery_address' => implode("\r\n", $delivery_address),

			'delivery_city' => $this->_basket['delivery_address']['town'],

			'delivery_postcode' => $this->_basket['delivery_address']['postcode'],

			'email_address' => $this->_basket['billing_address']['email'],

			'success_url' => $GLOBALS['storeURL'].'/index.php?_a=complete&amp;cart_order_id='.$this->_basket['cart_order_id'],

			'cancel_url' => $GLOBALS['storeURL'].'/index.php?_a=complete&amp;cmd=cancel',
 
			'callback_url' => $GLOBALS['storeURL'].'/index.php?_g=rm&amp;type=gateway&amp;cmd=call&amp;module=nochex',

			'test_transaction' => $test_trans,

			'test_success_url' => $GLOBALS['storeURL'].'/index.php?_a=complete&amp;cart_order_id='.$this->_basket['cart_order_id']
			
		);

		return $hidden;
	}

	public function call() {
		
		ini_set("SMTP","mail.nochex.com" ); 
		
		/* Checks to see if the order is / has been cancelled */

		if($_POST['cancelOrder'] == "Yes"){

			$order = Order::getInstance();

			$order_summary = $order->getSummary($_POST['$cart_order_id']);

			$order->orderStatus(Order::ORDER_CANCELLED, $cart_order_id);

			$order->paymentStatus(Order::PAYMENT_DECLINE, $cart_order_id);

		}

		// Get the POST information from Nochex server

		$postvars = http_build_query($_POST);		

		/* Checks to see if callback has been enabled in the shopping cart */

		/* Callback only works if this functionality has been abled by Nochex. */		

		if($_POST["optional_1"] == "Enabled"){

		 

		$url = "https://secure.nochex.com/callback/callback.aspx";

		  /*Callback URL Callback Curl - Post */

		$ch = curl_init ();

		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt ($ch, CURLOPT_POST, true);

		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postvars);

		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($ch);

		curl_close($ch);


		/* Checks to see if the transaction was a test or live transaction*/
		
		if($_POST["transaction_status"] == "100"){

		$testStatus = "Test";

		}else{

		$testStatus = "Live";

		}		

		// Put the variables in a printable format for the email

		/* Debug Information */

		$debug = "IP -> " . $_SERVER['REMOTE_ADDR'] ."\r\n\r\nPOST DATA:\r\n"; 

		foreach($_POST as $Index => $Value) 

		$debug .= "$Index -> $Value\r\n"; 

		$debug .= "\r\nRESPONSE:\r\n$response";

		$cart_order_id	= $_POST['order_id'];

		/* Retrieves the order, and order summary */

		$order = Order::getInstance();

		$order_summary = $order->getSummary($cart_order_id);


		/* Puts all the data into an array to update the order */

		$transData['gateway'] = "Nochex";

		$transData['customer_id'] = $order_summary["customer_id"];

		$transData['order_id'] = $cart_order_id;

		$transData['trans_id'] = $_POST["transaction_id"];

		$transData['amount'] = $_POST['amount'];

		$transData['status'] = $testStatus;

		$transData['extra'] = "";

		/* Checks the response from the server - Authorised or Declined */

		if ($response=="AUTHORISED") {

				/* Gathers all the information, and sets the status of the order to paid. */

				$msg = "Callback: " . $response . ",<br/>Transaction Status: " . $testStatus . "<br/>Transaction ID: ".$_POST["transaction_id"] . "<br/>Payment Received From: ".$_POST["email_address"] . "<br/>Total Paid: ".$_POST["gross_amount"];	

				$order->orderStatus(Order::ORDER_PROCESS, $cart_order_id);

				$order->paymentStatus(Order::PAYMENT_SUCCESS, $cart_order_id);		

				$transData['notes']= $msg;

				$order->logTransaction($transData);											

		} else { 

				/* Gathers all the information, and sets the status of the order to pending as this transaction was declined */

				$msg = "Callback: " . $response . ",<br/>Transaction Status: " . $testStatus . "<br/>Transaction ID: ".$_POST["transaction_id"] . "<br/>Payment Received From: ".$_POST["email_address"] . "<br/>Total Paid: ".$_POST["gross_amount"];	

				$order->orderStatus(Order::ORDER_PENDING, $cart_order_id);

				$order->paymentStatus(Order::PAYMENT_PENDING, $cart_order_id);	

				$transData['notes']= $msg;

				$order->logTransaction($transData);	

		}

		} else {

		/* APC Url */

		$url = "https://secure.nochex.com/apc/apc.aspx";

		/* Callback Curl - Post */

		// Curl code to post variables back

			$ch = curl_init(); // Initialise the curl tranfer
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars); // Set POST fields
			
		$output = curl_exec($ch); // Post back

		curl_close($ch);


		// Put the variables in a printable format for the email - Debug information

		$debug = ""; // = "IP -> " . $_SERVER['REMOTE_ADDR'] ."\r\n\r\nPOST DATA:\r\n"; 

		foreach($_POST as $Index => $Value) 

		$debug .= "$Index -> $Value\r\n"; 

		$debug .= "\r\nRESPONSE:\r\n$output";
		
		mail("james.lugton@nochex.com", "apc", $debug, "from:james.lugton@nochex.com");

		$cart_order_id	= $_POST['order_id'];

		/* Retrieves the order, and order summary */

		$order = Order::getInstance();

		$order_summary = $order->getSummary($cart_order_id);

		/* Puts all the data into an array to update the order */

		$transData['gateway'] = "Nochex";

		$transData['customer_id'] = $order_summary["customer_id"];

		$transData['order_id'] = $cart_order_id;

		$transData['trans_id'] = $_POST["transaction_id"];

		$transData['amount'] = $_POST['amount'];

		$transData['status'] = $_POST['status'];

		$transData['extra'] = "";

		/* Checks the response from the server - Authorised or Declined */

		if ($output == "AUTHORISED") {  

				/* Gathers all the information, and sets the status of the order to paid. */ 

				$msg = "APC was AUTHORISED. Status: " . $_POST['status'];	

				$order->orderStatus(Order::ORDER_PROCESS, $cart_order_id);

				$order->paymentStatus(Order::PAYMENT_SUCCESS, $cart_order_id);		

				$transData['notes'] = $msg;

				$order->logTransaction($transData);			

		} else { 
		
				/* Gathers all the information, and sets the status of the order to pending as this transaction was declined */ 

				$msg = "APC was not AUTHORISED. Status: " . $_POST['status'];  
						
				$order->orderStatus(Order::ORDER_PENDING, $cart_order_id);

				$order->paymentStatus(Order::PAYMENT_PENDING, $cart_order_id);	

				$transData['notes'] = $msg;

				$order->logTransaction($transData);	

		
		}
/**/
	}

}


	public function process() {

		httpredir(currentPage(array('_g', 'type', 'cmd', 'module'), array('_a' => 'complete')));

		return false;

	}
}
