<?php
namespace core\lib;

class Config
{
	private static $inited = false;
	private static $config = [];
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