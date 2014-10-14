<?php
namespace core\lib;
use core\main\libs;
class Request extends libs{
	protected function getvar($index, $ReqType, $TypeArray, $Required, $Clean, $Length, $Range, $Cases) {
		$Req = self::GetArrayByType ( $ReqType );
		
		$Check = $this->Check ( $index, $ReqType, $TypeArray );
		if(isset ( $Req [$index] )){
			$input =  $Req [$index];
			if( $Length !== FALSE)
				if( (is_string ( $input ) && (strlen ( $input ) > $Length[1] || strlen ( $input ) < $Length[0])))
					if($Required)
						$this->PrintLast ( Messages::$CheckInput);
			if($Range !== FALSE){
				if($input < $Range[0] || $input > $Range[1] )
					if($Required)
						$this->PrintLast ( Messages::$CheckInput );
				
			}
			if($Cases !== FALSE){
				if(!in_array($input, $Cases))
					if($Required)
						$this->PrintLast ( Messages::$CheckInput );
			
			}
		}
		if ($Required === TRUE && $Check === FALSE) {
			$this->PrintLast ( Messages::$CheckInput);
		}
		if ($Clean === TRUE && $Check === TRUE) {
			return Security::CleanXssString ( $Req [$index]);
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
}