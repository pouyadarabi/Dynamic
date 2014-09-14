<?php
namespace core\system;

class router
{
    public static function DoRoute()
    {
       	if(!empty($_SERVER [ 'QUERY_STRING' ]))
        	self::filter_uri( $_SERVER [ 'QUERY_STRING' ] );  
        
        $url = parse_url ( $_SERVER ['REQUEST_URI'], PHP_URL_PATH );
        $root = __Dynamic_PATH__;
        $url_string = strtolower(trim(str_replace($root, '', $url),'/'));
        $url_array = explode('/',$url_string);
        if ( empty( $url_string )) {
            $url_array [0] = 'main';
        }    
        if ( !self::ControllerIsValid( $url_array [0] ) ) {
            $url_array [0] = 'notfound';
        }    
        if ( empty( $url_array[1] ) ) {
            $url_array [ 1 ] = 'index';
        }        
        define ( '__REQ__CLASS__', $url_array [0]);
        define ( '__REQ__METHOD__', $url_array [1]);        
        $classname = '\\app\\controller\\'.__REQ__CLASS__;	
    	 try {
        	$method = new \ReflectionMethod($classname, __REQ__METHOD__);
        	$method->invoke(new $classname);
    	 } catch (\ReflectionException $e) {

    	 }
    }
    private static function ControllerIsValid($Controller_Name) {
    	return in_array ( $Controller_Name, $GLOBALS['CONFIG']['Controllers'] );
    }
    private static function filter_uri( $str )
    {
    	if (!empty($GLOBALS['CONFIG']['UrlAllowedChars'])) {
			$str = urldecode( $str );
    		if ( !preg_match( "|^[" . str_replace( array( '\\-', '\-' ), '-', preg_quote( $GLOBALS['CONFIG']['UrlAllowedChars'], '-' ) ) . "]+$|i", $str )) {
    			echo( '<h1>Bad Request</h1>' );
    			http_response_code( 400 );
    			die;
    		}
    	}
    }
}