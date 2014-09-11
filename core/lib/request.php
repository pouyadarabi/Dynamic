<?php
namespace core\lib;
use core\main\libs;
class Request extends libs{
	protected function getvar($index, $ReqType, $TypeArray, $Required, $Clean, $Length, $Range, $Cases) {
		$Req = self::GetArrayByType ( $ReqType );
		
		$Check = $this->Check ( $index, $ReqType, $TypeArray );
		if(isset ( $Req [$index] )){
			$input =  $Req [$index];
			if( (is_string ( $input ) && strlen ( $input ) > $Length))
				$this->PrintLast ( Messages::$CheckInput);
			if(count($Range) == 2){
				if($input < $Range[0] || $input > $Range[1] )
					$this->PrintLast ( Messages::$CheckInput );
			}
			if(count($Cases) > 0){
				if(!in_array($input, $Cases))
					$this->PrintLast ( Messages::$CheckInput );
			}
		}
		if ($Required === TRUE && $Check === FALSE) {
			$this->PrintLast ( Messages::$CheckInput);
		}
		if ($Clean === TRUE && $Check === TRUE) {
			return Security::CleanXssString ( $Req [$index] );
		}
		if ($Check === TRUE && isset ( $Req [$index] )) {
			return $Req [$index];
		} else
			return '';
	}
	private function GetArrayByType($Type) {
		switch ($Type) {
			case MyConsts::$Request_POST :
				return $_POST;
			case MyConsts::$Request_GET :
				return $_GET;
		}
	}
	private function Check($index, $ReqType, $VarType) {
		return Security::CheckInput ( $index, $ReqType, $VarType );
	}
}