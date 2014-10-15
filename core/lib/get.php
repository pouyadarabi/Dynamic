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
public function getSegment($segmentNumber){
	
		$url = parse_url( $_SERVER [ 'REQUEST_URI' ], PHP_URL_PATH );
		$url = str_ireplace(array(__REQ__METHOD__,__REQ__CLASS__,__Dynamic_PATH__), '', urldecode($url));
		$url = ltrim($url,'/');
		
		if($this->Required && $url == ''){
			$this->PrintLast ( Messages::get('CheckInput'));
		}
		if ($this->Type === false)
			$type = (isset ( $this->TypeArray [$this->MyCounter] ) ? $this->TypeArray [$this->MyCounter ++] : 's');
		else
			$type = $this->Type;
		$url = explode('/', $url);
		$data = $url[$segmentNumber - 1];
		return $this->CheckSegment($data, $type, $this->Required, $this->Clean);
		
	}
	private function CheckSegment($data, $VarType, $Required, $Clean) {
		$response = TRUE;
	
		if ( !is_array( $data ) && trim( $data ) == '' ) {
			$response =  FALSE;
		}
		
		if($response)
			if(Security::CheckType($data, $VarType) === false){
				$response = FALSE;
			}
	
		if ($Required === TRUE && $response === FALSE) {
			$this->PrintLast ( Messages::get('CheckInput'));
		}
		if ($Clean === TRUE && $response === TRUE){
				return Security::CleanXssString ( $data );
		}
		return $data;
	}
	public function isSeted() {
		return parent::CheckisSeted ( 'g' );
	}
}