<?php
require_once('IyzipayBootstrap.php');
IyzipayBootstrap::init();
class Config {
    public static function options() {
		$iyzico_api_key=tema_ayar("iyzico_api_key");
		$iyzico_secret_key=tema_ayar("iyzico_secret_key");
		$iyzico_sandbox=tema_ayar("iyzico_sandbox");
		if ($iyzico_sandbox=="açık") {
			$iyzico_base_url="https://sandbox-api.iyzipay.com";
		} else {
			$iyzico_base_url="https://api.iyzipay.com";
		}
        $options = new \Iyzipay\Options();
        $options->setApiKey($iyzico_api_key);
        $options->setSecretKey($iyzico_secret_key);		
        $options->setBaseUrl($iyzico_base_url);
        return $options;
    }
}