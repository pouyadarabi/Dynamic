<?php
namespace core\lib;
use core\main\libs;
class Helper extends libs{	
	/**
	 *
	 * @param array $array        	
	 * @return multitype:unknown
	 */
	public static function RemoveEmptyArray($array) {
		$count = count ( $array );
		$new = array ();
		for($i = 0; $i < $count; $i ++) {
			if (trim ( $array [$i] ) != '') {
				$new [] = $array [$i];
			}
		}
		return $new;
	}
	/**
	 *
	 * @param array $array        	
	 * @return multitype:unknown
	 */
	public static function RemoveNullArray($array) {
		$count = count ( $array );
		$new = array ();
		for($i = 0; $i < $count; $i ++) {
			if ($array [$i] != null) {
				$new [] = array_filter ( $array [$i], 'strlen' );
				
			}
		}
		return $new;
	}
	/**
	 *
	 * @param array $array        	
	 * @return string
	 */
	public static function Convert2Json($array, $header = true) {
		if ($array === false || $array == null) {
			$array = array ();
		}
		if ($header)
			header ( 'Content-type: application/json' );
		return json_encode ( $array );
	}
	
	/**
	 *
	 * @param array $array        	
	 * @param string $glue        	
	 * @return void string
	 */
	public static function Convert2String($array, $glue = '|') {
		if ($array == null)
			return;
		$ret_str = self::Merger ( $array, $glue );
		if (strrpos ( $ret_str, $glue ) != strlen ( $glue ))
			$ret_str = substr ( $ret_str, 0, - (strlen ( $glue )) );
		return $ret_str;
	}
	
	/**
	 *
	 * @param array $array        	
	 * @param string $glue        	
	 * @return string
	 */
	private static function Merger($array, $glue = '|') {
		$ret_str = '';
		foreach ( $array as $a ) {
			$ret_str .= (is_array ( $a )) ? self::Merger ( $a, $glue ) : strval ( $a ) . $glue;
		}
		
		return $ret_str;
	}
	public static function initUpload($fileElementName, $fileSize, $exten = false) {
		if (! isset ( $_FILES [$fileElementName] ))
			self::PrintLast ( Messages::get('FileUpload_Nofile') );
		
		if (($_FILES [$fileElementName] ['error'] != 0)) {
			switch ($_FILES [$fileElementName] ['error']) {
				
				case '1' :
					$error = Messages::get('FileUpload_MaxPHP');
					break;
				case '2' :
					$error = Messages::get('FileUpload_HTML');
					break;
				case '3' :
					$error = Messages::get('FileUpload_Partially');
					break;
				case '4' :
					$error = Messages::get('FileUpload_Nofile');
					break;
				
				case '6' :
					$error = Messages::get('FileUpload_MissingTemp');
					break;
				case '7' :
					$error = Messages::get('FileUpload_WriteDisk');
					break;
				case '8' :
					$error = Messages::get('FileUpload_BadExtention');
					break;
				case '999' :
				default :
					$error = Messages::get('FileUpload_NoCode');
			}
			self::PrintLast ( $error );
		} elseif (empty ( $_FILES [$fileElementName] ['tmp_name'] ) || $_FILES [$fileElementName] ['tmp_name'] == 'none') {
			self::PrintLast ( Messages::get('FileUpload_Nofile') );
		} else {
			$original_name = $_FILES [$fileElementName] ['name'];
			$name = Security::getInstance ()->CleanUploadsChar ( $_FILES [$fileElementName] ['name'] );
			$ext = strtolower ( substr ( $name, strlen ( $name ) - 3, 3 ) );
			if ($ext == null || trim ( $ext ) == '') {
				self::PrintLast ( Messages::get('BypassKicked') );
			}
			
			$size = $_FILES [$fileElementName] ['size'] / 1024;
			
			if ($size > $fileSize) {
				self::PrintLast ( Messages::get('MaxSizeFile') );
			}
			
			if ($exten !== false) {
				
				if (is_array ( $exten )) {
					if (! in_array ( $ext, $exten ))
						self::PrintLast ( Messages::get('FileUpload_BadExtention') );
				} else if ($exten != $ext) {
					self::PrintLast ( Messages::get('FileUpload_BadExtention') );
				}
			}
			return array (
					'file' => $_FILES [$fileElementName] ['tmp_name'],
					'name' => $name,
					'ext' => $ext 
			);
		}
	}
	public static function initMultiUpload($fileElementName, $fileSize, $exten, $max) {
		if (! isset ( $_FILES [$fileElementName] ) || ! is_array ( $_FILES [$fileElementName] ['name'] ))
			self::PrintLast ( Messages::get('FileUpload_Nofile') );
		$count = count ( $_FILES [$fileElementName] ['name'] );
		$files = array ();
		if ($count > $max) {
			self::PrintLast ( Messages::get('MaxSizeFile') );
		}
		for($i = 0; $i < $count; $i ++) {
			if (($_FILES [$fileElementName] ['error'] [$i] != 0)) {
				switch ($_FILES [$fileElementName] ['error'] [$i]) {
					case '1' :
						$error = Messages::get('FileUpload_MaxPHP');
						break;
					case '2' :
						$error = Messages::get('FileUpload_HTML');
						break;
					case '3' :
						$error = Messages::get('FileUpload_Partially');
						break;
					case '4' :
						$error = Messages::get('FileUpload_Nofile');
						break;
					
					case '6' :
						$error = Messages::get('FileUpload_MissingTemp');
						break;
					case '7' :
						$error = Messages::get('FileUpload_WriteDisk');
						break;
					case '8' :
						$error = Messages::get('FileUpload_BadExtention');
						break;
					case '999' :
					default :
						$error = Messages::get('FileUpload_NoCode');
				}
				self::PrintLast ( $error );
			} elseif (empty ( $_FILES [$fileElementName] ['tmp_name'] [$i] ) || $_FILES [$fileElementName] ['tmp_name'] [$i] == 'none') {
				self::PrintLast ( Messages::get('FileUpload_Nofile') );
			} else {
				$original_name = $_FILES [$fileElementName] ['name'] [$i];
				$name = Security::getInstance ()->CleanUploadsChar ( $_FILES [$fileElementName] ['name'] [$i] );
				$ext = strtolower ( substr ( $name, strlen ( $name ) - 3, 3 ) );
				if ($ext == null || trim ( $ext ) == '') {
					self::PrintLast ( Messages::get('BypassKicked') );
				}
				
				$size = $_FILES [$fileElementName] ['size'] [$i] / 1024;
				
				if ($size > $fileSize) {
					self::PrintLast ( Messages::get('MaxSizeFile') );
				}
				
				if ($exten !== false) {
					
					if (is_array ( $exten )) {
						if (! in_array ( $ext, $exten ))
							self::PrintLast ( Messages::get('FileUpload_BadExtention') );
					} else if ($exten != $ext) {
						self::PrintLast ( Messages::get('FileUpload_BadExtention') );
					}
				}
				$files [] = array (
						'file' => $_FILES [$fileElementName] ['tmp_name'] [$i],
						'name' => $name,
						'ext' => $ext 
				);
			}
		}
		return $files;
	}
	/**
	 *
	 * @param string $string        	
	 * @return boolean
	 */
	public static function is_utf8($string) {
		return (mb_detect_encoding ( $string, 'UTF-8', true ) == 'UTF-8');
	}
	public static function GetValidJson($key, $object, $value = NULL, $type = 2, $require = false) {
		if (isset ( $object->$key ) && Security::CheckType ( $object->$key, $type ))
			return $object->$key;
		else {
			if ($require === true)
				self::PrintLast ( Messages::get('checkinput') );
			else
				return $value;
		}
	}
	function CheckTime($time1, $time2) {
		$start = strtotime ( $time1 );
		$end = strtotime ( $time2 );
		if ($end - $start > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	/**
	 *
	 * @param int $Code        	
	 * @param string $text        	
	 * @return void
	 */
	public static function RaiseError($code, $text = '') {
		switch ($code) {
			case 400 :
				if (function_exists('http_response_code'))
					http_response_code ( 400 );
				echo '<title>400 Bad Request</title><h1>Bad Request</h1><p>Your browser sent a request that this server could not understand.<br></p>';
				break;
			case 404 :
				if (function_exists('http_response_code'))
					http_response_code ( 404 );
				echo '<title>404 Not Found</title><h1>Not Found</h1><p>The requested URL ' . $text . ' was not found on this server.</p>';
				break;
			case 403 :
				if (function_exists('http_response_code'))
					http_response_code ( 403 );
				echo '<title>403 Forbidden</title><h1>Forbidden</h1><p>You don\'t have permission to access ' . $text . ' this server.</p>';
				break;
			
			default :
				;
				break;
		}
		exit;
	}
	
	/**
	 *
	 * @param unknown $tm        	
	 * @param string $lang        	
	 * @param string $ashtml        	
	 * @return string
	 */
	function ago($tm, $lang = 'fa', $ashtml = true , $now = false) {
		if(!is_numeric($tm) ){
			$tm = strtotime($tm);
		}
		$local = array (
				'style' => array (
						'fa' => 'style="direction:rtl;"',
						'en' => 'style="direction:ltr"' 
				),
				'times' => array (
						'fa' => array (
								'ثانیه',
								'دقیقه',
								'ساعت',
								'روز',
								'هفته',
								'ماه',
								'سال',
								'دهه' 
						),
						'en' => array (
								'second',
								'minute',
								'hour',
								'day',
								'week',
								'month',
								'year',
								'decade' 
						) 
				),
				'ago' => array (
						'fa' => 'پیش',
						'en' => 'ago' 
				) 
		);
		if (intval ( $tm ) > 0) {
			if(!$now)
				$cur_tm = time ();
			else 
				$cur_tm = strtotime($now);
			$dif = $cur_tm - $tm;
			$lngh = array (
					1,
					60,
					3600,
					86400,
					604800,
					2630880,
					31570560,
					315705600 
			);
			for($v = sizeof ( $lngh ) - 1; ($v >= 0) && (($no = $dif / $lngh [$v]) <= 1); $v --)
				;
			if ($v < 0)
				$v = 0;
			$_tm = $cur_tm - ($dif % $lngh [$v]);
			$no = floor ( $no );
			if ($no != 1 && $lang == 'en')
				$local ['times'] [$lang] [$v] .= 's';
			$x = sprintf ( "%d %s ", $no, $local ['times'] [$lang] [$v] );
			if ($ashtml)
				return " " . $x . ' ' . $local ['ago'] [$lang] . "";
			else
				return $x . ' ' . $local ['ago'] [$lang];
		} else {
			return '-';
		}
	}
	
	/**
	 *
	 * @return Packages_Helper
	 */
	public static final function getInstance() {
		if (! self::$_instance) {
			self::$_instance = new self ();
		}
		
		return self::$_instance;
	}
}
