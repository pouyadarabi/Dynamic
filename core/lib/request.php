<?php

namespace core\lib;

use core\main\libs;

class Request extends libs {
	protected $Clean = TRUE;
	protected $Required = TRUE;
	protected $TypeArray = array ();
	protected $Type = false;
	protected $MyCounter = 0;
	protected $Range = FALSE;
	protected $Cases = FALSE;
	protected $Length = array ( 0, 100 );
	protected function getvar($index, $ReqType, $TypeArray, $Required, $Clean, $Length, $Range, $Cases) {
		$Req = self::GetArrayByType ( $ReqType );
		
		$Check = $this->Check ( $index, $ReqType, $TypeArray );
		if (isset ( $Req [$index] )) {
			$input = $Req [$index];
			if ($Length !== FALSE)
				if ((is_string ( $input ) && (strlen ( $input ) > $Length [1] || strlen ( $input ) < $Length [0])))
					if ($Required)
						$this->PrintLast ( Messages::get('checkinput') );
			if ($Range !== FALSE) {
				if ($input < $Range [0] || $input > $Range [1])
					if ($Required)
						$this->PrintLast ( Messages::get('checkinput') );
			}
			if ($Cases !== FALSE) {
				if (! in_array ( $input, $Cases ))
					if ($Required)
						$this->PrintLast ( Messages::get('checkinput') );
			}
		}
		if ($Required === TRUE && $Check === FALSE) {
			$this->PrintLast ( Messages::get('checkinput') );
		}
		if ($Clean === TRUE && $Check === TRUE) {
			return Security::CleanXssString ( $Req [$index] );
		}
		if ($Check === TRUE && isset ( $Req [$index] )) {
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