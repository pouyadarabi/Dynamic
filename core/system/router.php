<?php
namespace core\system;
use core\lib\Authorization;
class router
{
    public static function DoRoute()
    {
     
       	$url_string = '';
       	$url_array = array();
       	if(!ISCLI){
       	    if(!empty($_SERVER [ 'QUERY_STRING' ]) && !empty($GLOBALS['CONFIG']['UrlAllowedChars']))
       	        self::filter_uri( $_SERVER [ 'QUERY_STRING' ] );
       	    
            if(isset($_SERVER ['REQUEST_URI'])){
    	        $url = parse_url ( urldecode($_SERVER ['REQUEST_URI']), PHP_URL_PATH );
    	        $root = __Dynamic_PATH__;
    	        $url_string = strtolower(trim(str_ireplace($root, '', $url),'/'));
    	        $url_array = explode('/',$url_string);
            }
       	}
       
        if ( empty( $url_string )) {
            $url_array [0] = __Defualt_Controller__;
        }
        
        if ( !self::ControllerIsValid( $url_array [0] ) ) {
            $url_array [0] = 'notfound';
            $url_array [1] = __Defualt_Action__;
        }    
        if ( empty( $url_array[1] ) ) {
            $url_array [ 1 ] = __Defualt_Action__;
        }        
        define ( '__REQ__CLASS__', $url_array [0]);
        define ( '__REQ__METHOD__', $url_array [1]);    
        if(!ISCLI && $GLOBALS['CONFIG']['CheckAnnotations'] === true){
        	Authorization::DoAuthorization();
        }    
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
		$str = urldecode( $str );
    	if ( !preg_match( "|^[" . str_replace( array( '\\-', '\-' ), '-', preg_quote( $GLOBALS['CONFIG']['UrlAllowedChars'], '-' ) ) . "]+$|i", $str )) {
    		echo( '<h1>Bad Request</h1>' );
    		if (function_exists('http_response_code'))
    			http_response_code( 400 );
    		die;
    	}
    	
    }
}
