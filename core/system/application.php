<?php
namespace core\system;
use core\system\router;
use core\lib\Config;
class Application
{
    public static function initialize()
    {
        $config = Config::getAll();
        define('ISCLI', PHP_SAPI === 'cli');
        
        self::checkAllowedHosts($config['AllowedHosts']);
        $DynamicRoot = self::getSitePath();
        define( '__Dynamic_PATH__',$DynamicRoot );
        define( '__APP_PATH__', __SITE_PATH__.'/app' );
        define( '__View_PATH__', __APP_PATH__ . '/view/' );
        define('__Defualt_Controller__', $config['DefualtController']);
        define('__Defualt_Action__', $config['DefualtAction']);

        self::SecurityHeader();
        self::CheckReporting($config['DebugMode']);
        router::DoRoute();
       
    }
    /*
     * fix Host header attack
     */
    private static function checkAllowedHosts($hosts){
        if(ISCLI)
            return;
        if($hosts !== ''){
            $host = $_SERVER['HTTP_HOST'];
            $hosts = explode(',',$hosts);
            if(!in_array($host,$hosts)){
                die;
            }
        }
    }
    private static function SecurityHeader()
    {
        if(ISCLI)
            return;
        
        header( 'X-Frame-Options: SameOrigin' );
        header( 'X-XSS-Protection: 1' );
        header( 'X-Powered-By: Dynamic' );
        header( "Content_security_policy : default-src 'self' style-src 'self' 'unsafe-inline';" );
    }

    private static function CheckReporting($status)
    {
        if ($status === TRUE ) {
            error_reporting( E_ALL );
            ini_set( 'display_errors', 'On' );
        } else {
            error_reporting( 0 );
            ini_set( 'display_errors', 'Off' );
        }
    }
    
    private static function getSitePath(){
		$folder = ltrim(dirname($_SERVER['SCRIPT_NAME']),'/');
		return $folder == '' ? $folder : $folder. '/';
    }

}
