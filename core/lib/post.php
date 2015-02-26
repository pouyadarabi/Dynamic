<?php

namespace core\lib;

class POST extends Request {
	
	private static $_instance;
	public static final function getInstance() {
		if (! self::$_instance) {
			self::$_instance = new self ();
		}
		
		return self::$_instance;
	}
	public function get($index,$default = '') {
	    return parent::getvar ( $index, 'p', $this->Required, $this->Clean, $this->Length, $this->Range, $this->Cases,$default);
	}
	public function isSeted() {
		return parent::CheckisSeted ( 'p' );
	}
}