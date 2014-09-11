<?php
namespace core\lib;
class GET extends Request {
	
	private $Clean = FALSE;
	private $Required = TRUE;
	private $TypeArray = [];
	private $MyCounter = 0;
	private $Range =  [];
	private $Cases =  [];
	private $Length = 100;
	private static $_instance;
	
	public static final function getInstance() {
		if (! self::$_instance) {
			self::$_instance = new self ();
		}
		
		return self::$_instance;
	}
	public function get($index){
		
		$type = (isset($this->TypeArray[$this->MyCounter]) ? $this->TypeArray[$this->MyCounter++] : 's') ;
		
		$res =  parent::getvar ( $index, MyConsts::$Request_GET,$type , $this->Required, $this->Clean,$this->Length,$this->Range,$this->Cases );
		$this->Range = [];
		$this->Cases = [];
		return  $res;
	}
	
	public function isSeted() {
		if(count($_GET) > 0)
			return true;
		else return false;
	}
		/**
	 * @param int $start
	 * @param int $end
	 */
	public function SetRange($start,$end) {
		$this->Range = [$start,$end];
		return $this;
	}
	public function SetWhiteList(array $array) {
		$this->Cases = $array;
		return $this;
	}
	/**
	 * @param boolean $Clean
	 */
	public function setClean($Clean) {
		$this->Clean = $Clean;
		return $this;
	}
	
	/**
	 * @param boolean $Required
	 */
	public function setRequired($Required) {
		$this->Required = $Required;
		return $this;
	}
	
	/**
	 * @param mixed: $Type
	 */
	public function setType($Type) {
		$this->TypeArray = $Type;
		return $this;
	}
	
	/**
	 * @param number $Length
	 */
	public function setLength($Length) {
		$this->Length = $Length;
		return $this;
	}
	
}