<?php
namespace core\lib;
class GET extends Request {
	
private $Clean = TRUE;
	private $Required = TRUE;
	private $TypeArray = [];
	private $Type = false;
	private $MyCounter = 0;
	private $Range =  FALSE;
	private $Cases =  FALSE;
	private $Length = [0, 100];
	private static $_instance;
	
	public static final function getInstance() {
		if (! self::$_instance) {
			self::$_instance = new self ();
		}
		
		return self::$_instance;
	}
	public function get($index){
		
		if($this->Type === false)
			$type = (isset($this->TypeArray[$this->MyCounter]) ? $this->TypeArray[$this->MyCounter++] : 's') ;
		else 
			$type = $this->Type;
		$res =  parent::getvar ( $index,'g',$type , $this->Required, $this->Clean,$this->Length,$this->Range,$this->Cases );
		$this->Range = FALSE;
		$this->Cases = FALSE;
		$this->Length = FALSE;
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
		if(is_array($Type)){
			$this->TypeArray = $Type;
		}
		else{
			$this->Type = $Type;
		}		
		return $this;
	}
	
	/**
	 * @param number $MinLength
	 * @param number $MaxLength
	 */
	public function setLength($MinLength,$MaxLength) {
		$this->Length = [$MinLength,$MaxLength];
		return $this;
	}
}