<?php
namespace core\system;
use core\system\router;
class Application
{
    public static function initialize()
    {
        $GLOBALS['CONFIG'] =  include dirname( __FILE__ ).'/../config/appconfig.php';
        define('ISCLI', PHP_SAPI === 'cli');
        self::checkAllowedHosts();
        $DynamicRoot = self::getSitePath();
        define( '__Dynamic_PATH__',$DynamicRoot );
        define( '__APP_PATH__', __SITE_PATH__.'/app' );
        define( '__View_PATH__', __APP_PATH__ . '/view/' );
        define('__Defualt_Controller__', $GLOBALS['CONFIG']['DefualtController']);
        define('__Defualt_Action__', $GLOBALS['CONFIG']['DefualtAction']);
        self::SecurityHeader();
        self::CheckReporting();
        router::DoRoute();
       
    }
    /*
     * fix Host header attack
    */
    private static function checkAllowedHosts(){
        if(ISCLI)
            return;
        $hosts = $GLOBALS['CONFIG']['AllowedHosts'];
        if($hosts !== ''){
            $host = $_SERVER['HTTP_HOST'];
            $hosts = explode(',',$hosts);
            if(!in_array($host,$hosts)){
                die;
            }
        }
    }
    public static function SecurityHeader()
    {
        if(ISCLI)
            return;
        header( 'X-Frame-Options: SameOrigin' );
        header( 'X-XSS-Protection: 1' );
        header( 'X-Powered-By: Dynamic' );
        header( "Content_security_policy : default-src 'self' style-src 'self' 'unsafe-inline';" );
    }

    public static function CheckReporting()
    {
    	$status = $GLOBALS['CONFIG']['DebugMode'];
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
		return $folder . '/';
    }

}