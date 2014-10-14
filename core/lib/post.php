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
	public function get($index) {
		if ($this->Type === FALSE)
			$type = (isset ( $this->TypeArray [$this->MyCounter] ) ? $this->TypeArray [$this->MyCounter ++] : 's');
		else
			$type = $this->Type;
		$res = parent::getvar ( $index, 'p', $type, $this->Required, $this->Clean, $this->Length, $this->Range, $this->Cases );
		$this->Range = FALSE;
		$this->Cases = FALSE;
		$this->Length = FALSE;
		return $res;
	}
	public function isSeted() {
		return parent::CheckisSeted ( 'p' );
	}
}