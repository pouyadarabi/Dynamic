<?php
namespace core\system;
use core\lib\Config;

class Router {

    public static function DoRoute() {
        $config = Config::getAll();
        
        $url_string = '';
        $url_array = [];
        
        if (! ISCLI) {
            if (! empty($_SERVER['QUERY_STRING']) &&
                     ! empty($config['UrlAllowedChars']))
                self::filter_uri($_SERVER['QUERY_STRING'], $config['UrlAllowedChars']);
            
            if (isset($_SERVER['REQUEST_URI'])) {
                $url = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
                $root = __Dynamic_PATH__;
                $url_string = strtolower(trim(str_ireplace($root, '', $url), '/'));
                $url_array = explode('/', $url_string);
            }
        }
        if (empty($url_string)) {
            $url_array[0] = __Defualt_Controller__;
        }
      
        if ($config['URLMapping'] == true) {
            self::ParseUrlMapping($url_array);
        } else {
            if (! self::ControllerIsValid($url_array[0], $config['Controllers'])) {
                $url_array = ['notfound',__Defualt_Action__];
            }
        }
        
       
        if (empty($url_array[1])) {
            $url_array[1] = __Defualt_Action__;
        }
        
        $class = $url_array[0];
        $method = $url_array[1];
      
        if (! ISCLI && $config['CheckAnnotations'] == true) {
            GateWay::check($class,$method);
        }
        $classname = '\\app\\controller\\'.$class;
      
        try {
            $method = new \ReflectionMethod($classname, $method);
        } catch (\ReflectionException $e) {
            $class = 'notfound';
            $classname = '\\app\\controller\\'.$class;
            $method = new \ReflectionMethod($classname, __Defualt_Action__);
            	
        }
        define ( '__REQ__CLASS__', $class);
        define ( '__REQ__METHOD__', $method);
        
        $method->invoke(new $classname);
        exit();    
    }

    private static function ControllerIsValid ($Controller_Name, $allowedControllers) {
        return in_array(strtolower($Controller_Name), $allowedControllers);
    }

    private static function filter_uri ($str, $allowedChars) {
        $str = urldecode($str);
        if (! preg_match("|^[" .
                 str_replace(['\\-','\-'], '-', preg_quote($allowedChars, '-')) .
                 "]+$|i", $str)) {
            echo ('<h1>Bad Request</h1>');
            http_response_code(400);
            die();
        }
    }

    private static function ParseUrlMapping (&$url_array) {
        $checkString = implode('/', $url_array);
        $mapFile = file_get_contents(dirname(__FILE__) . '/../config/urlmap.json');
        $maps = json_decode($mapFile, true);
        $checkString = strtolower($checkString);
        
        $maps = array_change_key_case($maps);
      
        if (array_key_exists($checkString, $maps)) {
            $map = $maps[$checkString];
            
            if (is_string($map)) {
                $url_array = explode('/', $map);
                return;
            } else {
                foreach ($map as $action) {
                    $method = $action['method'];
                    $controller = $action['controller'];
                    if ($method == __REQUEST_METHOD__) {
                        $url_array = explode('/', $controller);
                        return;
                    }
                }
            }
        } else {
            $checkString = $url_array[0];
            if (array_key_exists($checkString, $maps)) {
                $map = $maps[$checkString];
                $url_array = [$map,$url_array[1]];
                return;
            }
        }
        $url_array = ['notfound',__Defualt_Action__];
    }
}
