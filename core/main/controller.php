<?php
namespace core\main;
class Controller extends AbstractClass
{

    public function __construct()
    {
    }

    protected function Redirect ($url = '', $append = '')
    {
        $url  = $this->site_url($url);
        header('location: ' . $url.$append);
        exit();
    }

    protected function site_url ($url = '')
    {      
        if (parse_url($url, PHP_URL_SCHEME) != '')
            return $url;
        if ($url == ''){
            $url = __Defualt_Controller__;
        }
        $protocol = ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $siteURL = $protocol . $_SERVER['HTTP_HOST'] . '/' . __Dynamic_PATH__;
        $url = str_ireplace(__Dynamic_PATH__, '', $url);
        $url = ltrim($url, '/');
        return $siteURL . $url;
    }
}
