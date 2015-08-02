<?php

namespace core\lib;


class Messages {
	
	/**
	 * @param string $key
	 * @return string
	 */
	public static function get($key) {
		$configFile = file_get_contents(dirname( __FILE__ ).'/../config/messages.json');
		$data = json_decode($configFile,true);
		$lang = Config::get('Lang');
		$key = strtolower($key);
		$Messages = $data[$lang];
		$Messages = array_change_key_case($Messages);
		return $Messages[$key];	
	}
	
}