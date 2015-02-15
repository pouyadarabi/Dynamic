<?php

namespace core\lib;

use core\main\libs;

class Request extends libs {
	protected $Clean = true;
	protected $Required = true;
	protected $TypeArray = array ();
	protected $Type = false;
	protected $MyCounter = 0;
	protected $Range = false;
	protected $Cases = false;
	protected $Length = array ( 0, 100 );
	protected function getvar($index, $ReqType, $TypeArray, $Required, $Clean, $Length, $Range, $Cases) {
		$Req = self::GetArrayByType ( $ReqType );
		
		$Check = $this->Check ( $index, $ReqType, $TypeArray );
		if (isset ( $Req [$index] )) {
			$input = $Req [$index];
			
			if ($Required && trim($input) == ''){	    
			    $this->PrintLast ( Messages::get('checkinput') );
			}
			if ($Length !== false){
				if ((is_string ( $input ) && (strlen ( $input ) > $Length [1] || strlen ( $input ) < $Length [0])))
				    $this->PrintLast ( Messages::get('checkinput') );
			}
			if ($Range !== false) {
				if ($input < $Range [0] || $input > $Range [1])
					$this->PrintLast ( Messages::get('checkinput') );
			}
			if ($Cases !== false) {
				if (! in_array ( $input, $Cases ))
					$this->PrintLast ( Messages::get('checkinput') );
			}
		}
		if ($Required === true && $Check === false) {
			$this->PrintLast ( Messages::get('checkinput') );
		}
		if ($Clean === true && $Check === TRUE) {
			return Security::CleanXssString ( $Req [$index] );
		}
		if ($Check === true && isset ( $Req [$index] )) {
			return $Req [$index];
		} else
			return false;
	}
	private function GetArrayByType($Type) {
		switch ($Type) {
			case 'p' :
				return $_POST;
			case 'g' :
				return $_GET;
		}
	}
	private function Check($index, $ReqType, $VarType) {
		return Security::CheckInput ( $index, $ReqType, $VarType );
	}
	public function CheckisSeted($type = 'p') {
		$array = $this->GetArrayByType ( $type );
		if (count ( $array ) > 0)
			return true;
		else
			return false;
	}
	/**
	 *
	 * @param int $start        	
	 * @param int $end        	
	 */
	public function SetRange($start, $end) {
		$this->Range = array (
				$start,
				$end 
		);
		return $this;
	}
	public function SetWhiteList(array $array) {
		$this->Cases = $array;
		return $this;
	}
	/**
	 *
	 * @param boolean $Clean        	
	 */
	public function setClean($Clean) {
		$this->Clean = $Clean;
		return $this;
	}
	
	/**
	 *
	 * @param boolean $Required        	
	 */
	public function setRequired($Required) {
		$this->Required = $Required;
		return $this;
	}
	
	/**
	 *
	 * @param mixed: $Type        	
	 */
	public function setType($Type) {
		if (is_array ( $Type )) {
			$this->TypeArray = $Type;
		} else {
			$this->Type = $Type;
		}
		return $this;
	}
	
	/**
	 *
	 * @param number $MinLength        	
	 * @param number $MaxLength        	
	 */
	public function setLength($MinLength, $MaxLength) {
		$this->Length = array ( $MinLength,$MaxLength );
		return $this;
	}
}