<?php

namespace core\lib;

use core\main\libs;

class Messages extends libs {
	
	/**
	 * @param string $key
	 * @return string
	 */
	public static function get($key) {
		$xmlFile =  dirname( __FILE__ ).'/../config/messages.xml';
		$xml = simplexml_load_file($xmlFile);
		$json = json_encode($xml);
		$xml = json_decode($json,TRUE);
		$lang = $GLOBALS['CONFIG']['Lang'];
		$key = strtolower($key);
		$Messages = $xml[$lang];
		$Messages = array_change_key_case($Messages);
		return $Messages[$key];
	
	}
	
}