<?php
namespace core\main;
class Controller extends AbstractClass
{

    public function __construct()
    {
    }

    protected function Redirect( $url = '', $append = '' )
    { 	
        if ( trim( $url == '' ) ) {
        	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        	$siteURL =  $protocol . $_SERVER[ 'HTTP_HOST' ] . '/' . __Dynamic_PATH__;
            $url = $siteURL . 'main' . $append;
        }
        $url = $this->site_url($url);
        header( 'location: ' . $url );
        exit;
    }
    protected function site_url( $url )
    {
    	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    	$siteURL =  $protocol . $_SERVER[ 'HTTP_HOST' ] . '/' . __Dynamic_PATH__;
    	if (parse_url($url, PHP_URL_SCHEME) != '') 
    		return $url;
    	$url = str_replace(__Dynamic_PATH__, '', $url);
    	$url = ltrim($url,'/');
    	return $siteURL . $url;
    }
}