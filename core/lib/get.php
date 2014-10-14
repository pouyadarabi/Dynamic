<?php
namespace core\lib;
class GET extends Request {
	private static $_instance;
	public static final function getInstance() {
		if (! self::$_instance) {
			self::$_instance = new self ();
		}
		
		return self::$_instance;
	}
	public function get($index) {
		if ($this->Type === false)
			$type = (isset ( $this->TypeArray [$this->MyCounter] ) ? $this->TypeArray [$this->MyCounter ++] : 's');
		else
			$type = $this->Type;
		$res = parent::getvar ( $index, 'g', $type, $this->Required, $this->Clean, $this->Length, $this->Range, $this->Cases );
		$this->Range = FALSE;
		$this->Cases = FALSE;
		$this->Length = FALSE;
		return $res;
	}
	public function isSeted() {
		return parent::CheckisSeted ( 'g' );
	}
}