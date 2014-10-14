<?php
namespace core\main;
class AbstractClass
{
    protected static function PrintLast( $msg, $isError = TRUE )
    {
        if ( !$isError )
            echo $msg;
        else {
        	if (function_exists('http_response_code'))
            	http_response_code( 500 );
            echo $msg;
        }
        die;
    }
}
