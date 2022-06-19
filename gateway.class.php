<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@devellion.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 */

// use AdamStipak\Webpay\PaymentRequest;
// require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/signature.php';

class Gateway {
	private $_module;
	private $_basket;

	// private $signer;
	// private $api;

	private $dbgEnabled = true;
	private $merchantNumber;
	private $webpayUrl;
	private $privateKeyFilepath;
	private $privateKeyPassword;
	private $publicKeyFilepath;
	private $publicGpKeyFilepath;

	private function dbg($msg) {
		if ($this->dbgEnabled) {
			error_log("GPWebpay: $msg");
		}
	}

	public function __construct($module = false, $basket = false) {
		$this->_module	= $module;
		$this->_basket =& $GLOBALS['cart']->basket;

		$this->merchantNumber = $this->_module['merchantNumber'];
		$this->privateKeyPassword  = $this->_module['privateKeyPassword'];
		$this->privateKeyFilepath  = __DIR__ . '/keys/' . $this->_module['privateKeyFilename'];
		$this->publicKeyFilepath   = __DIR__ . '/keys/' . $this->_module['publicKeyFilename'];
		$this->publicGpKeyFilepath = __DIR__ . '/keys/' . $this->_module['publicGpKeyFilename'];
		$this->webpayUrl = $this->_module['environment'] == 'testing'
			? 'https://test.3dsecure.gpwebpay.com/pgw/order.do'
			: 'https://3dsecure.gpwebpay.com/pgw/order.do';
		self::dbg('environment:' . $this->_module['environment']);
		self::dbg('webpayUrl:' . $this->webpayUrl);
		self::dbg('module:' . json_encode($this->_module));

		self::dbg('GPWebpay_dbg privkey:'.$this->privateKeyFilepath);
		// $this->signer = new \AdamStipak\Webpay\Signer(
		// 	$this->privateKeyFilepath,    // Path of private key.
		// 	$this->privateKeyPassword,    // Password for private key.
		// 	$this->publicKeyFilepath      // Path of public key.
		// );

		// $this->api = new \AdamStipak\Webpay\Api(
		// 	// $this->_module['merchantNumber'],    // Merchant number.
		// 	$this->merchantNumber,    // Merchant number.
		// 	$this->webpayUrl,         // URL of webpay.
		// 	$this->signer             // instance of \AdamStipak\Webpay\Signer.
		// );
	}

	##################################################

	public function transfer() {
		self::dbg('GPWebpay_dbg transfer()');
		$transfer	= array(
			'action'	=> $this->webpayUrl,
			'method'	=> 'post',
			'target'	=> '_self',
			'submit'	=> 'auto',
		);
		return $transfer;
	}

	public function repeatVariables() {
		return false;
	}

	private static function cartorderid_to_ordernumber($cartorderid) {
		// GP Webpay's MERORDERNUM may contain only digits
		$id = str_replace("-", "", $cartorderid);
		// GP Webpay's ORDERNUMBER has a limit of 15, digits-only
		// So we will cut out the last digit of the 16-digit CubeCart's order_id,
		// which consists of 6 digits for date, 6 digits for time, 4 random digits
		// And the last one of the 4 random digits is cut off here. which shouldn't cause any problem.
		return substr($id, 0, 15);
	}
	private static function currency_str_to_code($currencyStr) {
		switch ($currencyStr) {
			case 'EUR': return 978;
			case 'CZK': return 203;
			case 'GBP': return 826;
			case 'HUF': return 348;
			case 'PLN': return 985;
			case 'RUB': return 643;
			case 'USD': return 840;
		}
		trigger_error("Currency '$currencyStr' not supported by the GPWebpay module.", E_USER_ERROR);	
	}

	public function fixedVariables() {
		self::dbg('GPWebpay_dbg fixedVariables()');
		$GLOBALS['config']->set('config', 'csrf', '0'); // Prevent token field

		self::dbg('GPWebpay_dbg transfer() basket:' . json_encode($this->_basket));
		$ordernumber = self::cartorderid_to_ordernumber($this->_basket['cart_order_id']);
		$merchant_data = $this->_basket['cart_order_id'];

		// Use the CubeCart's default currency also for the GPWebpay's currency
		$currency_str = $GLOBALS['config']->get('config', 'default_currency');
		$currency_code = self::currency_str_to_code($currency_str);
		self::dbg("currency_str:$currency_str code:$currency_code");

		$operation = 'CREATE_ORDER';
		// $currency_code = '978';
		$depositflag = '1';
		$amount_total = (int) round(floatval($this->_basket['total']) * 100);
		// $amount_total = int(float($this->_basket['total']) * 100);
		// $return_url = 'http://localhost:80/demoshop_code/index.php?action=response';
		$return_url = $GLOBALS['storeURL'].'/index.php?_g=rm&type=gateway&cmd=process&module=GPWebpay';
		$digest_str = implode('|', [$this->merchantNumber, $operation, $ordernumber, $amount_total, $currency_code, $depositflag, $return_url, $merchant_data]);
		$sign = new CSignature($this->privateKeyFilepath, $this->privateKeyPassword, $this->publicKeyFilepath);
		$signature = $sign->sign($digest_str);
		// self::dbg('GPWebpay_dbg transfer() url:'.$url);

		$hidden	= array(
			'MERCHANTNUMBER' => $this->merchantNumber,
			'OPERATION' => $operation,
			'ORDERNUMBER' => $ordernumber,
			'AMOUNT' => $amount_total,
			'CURRENCY' => $currency_code,
			'DEPOSITFLAG' => $depositflag,
			'URL' => $return_url,
			'MD' => $merchant_data,
			'DIGEST' => $signature,
		);

		// $request = new PaymentRequest(
		// 	$ordernumber,
		// 	$this->_basket['total'],
		// 	$currency_code,
		// 	$depositflag,
		// 	$GLOBALS['storeURL'].'/index.php?_g=rm&type=gateway&cmd=process&module=GPWebpay',
		// 	null,
		//  $merchant_data);

		// $url = $this->api->createPaymentRequestUrl($request);
		// $hidden = $this->api->createPaymentParam($request);
		// self::dbg("createPaymentParam(): ".json_encode($hidden));

		
		return (isset($hidden)) ? $hidden : false;
	}

	##################################################

	public function call() {
		self::dbg('GPWebpay_dbg call()');
		return false;
	}

	public function process() {
		self::dbg('GPWebpay_dbg process() GET:'.json_encode($_REQUEST));
		$operation         = $_REQUEST['OPERATION'];
		$order_id          = $_REQUEST['ORDERNUMBER'];
		$merchant_data     = $_REQUEST['MD'];
		$prcode            = $_REQUEST['PRCODE'];
		$srcode            = $_REQUEST['SRCODE'];
		$resulttext        = $_REQUEST['RESULTTEXT'];
		$digest            = $_REQUEST['DIGEST'];
		$digest1           = $_REQUEST['DIGEST1'];

		$sign = new CSignature($this->privateKeyFilepath, $this->privateKeyPassword, $this->publicGpKeyFilepath);
		$digest_str = implode('|', [$operation, $order_id, $merchant_data, $prcode, $srcode, $resulttext]);
		$verify = $sign->verify($digest_str, $digest);
		self::dbg('GPWebpay_dbg process() digest_str:'.$digest_str);
		self::dbg('GPWebpay_dbg process() digest:'.$digest);
		self::dbg('GPWebpay_dbg process() verify:'.$verify);
		$digest1_str = implode('|', [$operation, $order_id, $merchant_data, $prcode, $srcode, $resulttext, $this->merchantNumber]);
		$verify1 = $sign->verify($digest1_str, $digest1);
		self::dbg('process() verify1:'.$verify1);

		$cart_order_id = $merchant_data;
		self::dbg('process() cart_order_id:'.$cart_order_id);

		$order				= Order::getInstance();
		$order_summary		= $order->getSummary($cart_order_id);
		self::dbg('process() order_details:'.json_encode($order->getOrderDetails($cart_order_id)));
		self::dbg('process() order_id:'.$order_id);
		self::dbg('process() merchant_data:'.$merchant_data);
		self::dbg('process() order_summary:'.$order_summary);
		self::dbg('process() credit_card_processed:'.$_REQUEST['credit_card_processed']);
		
		if($prcode==0 && $srcode==0 && $verify==1){
			self::dbg('GPWebpay_dbg process() order was successfully processed');
			$notes 	= 'Card was successfully processed.';
			$status = 'Processed';
			$order->orderStatus(Order::ORDER_PROCESS, $cart_order_id);
			$order->paymentStatus(Order::PAYMENT_SUCCESS, $cart_order_id);
		} else {
			self::dbg('GPWebpay_dbg process() order was not processed');
			$notes = 'Card has not yet been processed and is currently pending.';
			$status = 'Pending';
			$order->orderStatus(Order::ORDER_PENDING, $cart_order_id);
			$order->paymentStatus(Order::PAYMENT_PENDING, $cart_order_id);
		}

		if ($prcode != 0 || $srcode != 0) {
			$error_str = "GP webpay gateway returned an error (prcode:$prcode srcode:$srcode).";
			if ($prcode == 14) {
				$error_str .= " Duplicate order number.";
			}
			$GLOBALS['gui']->setError($error_str);
			$notes .= " ERROR: $error_str";
		}

		$transData['notes']			= $notes;
		$transData['gateway']		= $_REQUEST['module'];
		$transData['order_id']		= $cart_order_id;
		$transData['trans_id']		= $_REQUEST['order_number'];
		$transData['amount']		= isset($_REQUEST['total']) ? $_REQUEST['total'] : '';
		$transData['status']		= $status;
		$transData['customer_id']	= $order_summary['customer_id'];
		$transData['extra']			= '';
		self::dbg('GPWebpay_dbg process() transData:'.json_encode($transData));
		$order->logTransaction($transData);


		// The basket will be emptied when we get to _a=complete, and the status isn't Failed/Declined

		// Redirect to _a=complete, and drop out unneeded variables
		httpredir(currentPage(array('_g', 'type', 'cmd', 'module'), array('_a' => 'complete')));
	}

	public function form() {
		self::dbg('GPWebpay_dbg form()');
		return false;
	}
}