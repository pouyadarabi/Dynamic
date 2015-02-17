<?php
namespace core\lib;
use core\main\libs;
class Session extends libs
{
	private static $inited = false;
	
    public static function init()
    {
        $config = Config::getAll();
    	session_name($config['AppName']);
    	
        if ( session_id() == '' )
            session_start();
        
        if ( isset( $_SESSION[ 'fingerprint' ] ) ) {
            $var = false;
            if ( $config[ 'Session_Secure' ] ) {
                $var = md5( $_SERVER[ 'HTTP_USER_AGENT' ] . '__+#@!%^+__' . $_SERVER[ 'REMOTE_ADDR' ] );
            } else {
                if (  $config[ 'Session_UserAgentCheck' ] ) {
                    $var = md5( $_SERVER[ 'HTTP_USER_AGENT' ] . '__+#@!%^+__' );
                } else {
                    if ( $config[ 'Session_IPCheck' ] ) {
                        $var = md5( $_SERVER[ 'REMOTE_ADDR' ] . '__+#@!%^+__' );
                    }
                }
            }
            if ( $_SESSION[ 'fingerprint' ] != $var ) {
                self::Destroy();
                die;
            }


        } else {
            if ( $config[ 'Session_Secure' ] ) {
                $_SESSION[ 'fingerprint' ] = md5( $_SERVER[ 'HTTP_USER_AGENT' ] . '__+#@!%^+__' . $_SERVER[ 'REMOTE_ADDR' ] );
            } else {
                if ( $config[ 'Session_UserAgentCheck' ] ) {
                    $_SESSION[ 'fingerprint' ] = md5( $_SERVER[ 'HTTP_USER_AGENT' ] . '__+#@!%^+__' );
                } else {
                    if ( $config[ 'Session_IPCheck' ] ) {
                        $_SESSION[ 'fingerprint' ] = md5( $_SERVER[ 'REMOTE_ADDR' ] . '__+#@!%^+__' );
                    }
                }
            }
        }
        
        self::$inited = true;
    }

    public static function Destroy()
    {
    	if(!self::$inited)
    		self::init();
        session_destroy();
        $_SESSION = array();
    }

    public static function get( $index )
    {
    	if(!self::$inited)
    		self::init();
    	if(isset($_SESSION[ $index ]))
        	return Security::CleanXssString( $_SESSION[ $index ] );
    	return '';
    }

    public static function set( $index, $value )
    {
    	if(!self::$inited)
    		self::init();
        $_SESSION[ $index ] = Security::CleanXssString( $value );
    }
    public static function __setArray( $index, $value )
    {
    	if(!self::$inited)
    		self::init();
        $_SESSION[ $index ][] = Security::CleanXssString( $value );
    }
    public static function __unsetArray( $key1, $key2 )
    {
    	if(!self::$inited)
    		self::init();
        unset( $_SESSION[ $key1 ][$key2] );
    }
    public static function UnsetSession( $SessionName )
    {
    	if(!self::$inited)
    		self::init();
        unset( $_SESSION[ $SessionName ] );
    }

    public static function Check( $Name )
    {
    	if(!self::$inited)
    		self::init();
        if ( !isset( $_SESSION[ $Name ] ) || ( trim( $_SESSION[ $Name ] ) == '' ) ) {

            return false;
        }
        return true;
    }

    public static function ReGenarate()
    {
    	if(!self::$inited)
    		self::init();
        session_regenerate_id();
    }
}