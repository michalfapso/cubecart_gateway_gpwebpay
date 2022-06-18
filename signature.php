<?php
class CSignature{
	var $privateKey, $privateKeyPassword, $publicKey;
  // parametry: jmeno souboru soukromeho klice, privateKeyPassword k soukromemu klici, jmeno souboru s publicKeym klicem
  // params: name of the private key, private key password, name of the public key
  // function CSignature($privateKey="./key/test_key.pem", $privateKeyPassword="111111", $publicKey="./key/gpe.signing_test.pem"){
	function __construct($privateKey, $privateKeyPassword, $publicKey){
	  $fp = fopen($privateKey, "r");
	  $this->privateKey = fread($fp, filesize($privateKey));
	  fclose($fp);
	  $this->privateKeyPassword=$privateKeyPassword;

	  $fp = fopen($publicKey, "r");
	  $this->publicKey = fread($fp, filesize($publicKey));
	  fclose($fp);
	}
	
	function sign($text){
	//   echo "TEST sign()<br\>";
	  error_log("TEST sign()");
	  $pkeyid = openssl_get_privatekey($this->privateKey, $this->privateKeyPassword);
	//   echo "openssl_error_string:".openssl_error_string()."<br\>";
	  error_log("openssl_error_string:".openssl_error_string());
	  openssl_sign($text, $signature, $pkeyid);
	  $signature = base64_encode($signature);
	  openssl_free_key($pkeyid);
	  return $signature;
	}
	
	function verify($text, $signature){
	  $pubkeyid = openssl_get_publickey($this->publicKey);
	  $signature = base64_decode($signature);
	  $result = openssl_verify($text, $signature, $pubkeyid);
	  openssl_free_key($pubkeyid);
	  return (($result==1) ? true : false);
	}
}
?>
