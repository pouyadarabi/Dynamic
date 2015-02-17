<?php
namespace core\lib;
use core\main\libs;
class Config extends libs
{
	private static $inited = false;
	private static $config = array();
    public static function init()
    {
    
        self::$config =  include dirname( __FILE__ ).'/../config/appconfig.php';
        self::$inited = true;
    }

    public static function get( $index )
    {
    	if(!self::$inited)
    		self::init();
    	$config = self::$config;
    	return $config[$index];
    
    }
    
    public static function getAll( )
    {
        if(!self::$inited)
            self::init();
        return self::$config;
    
    }

  
}