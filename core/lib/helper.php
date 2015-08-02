<?php
namespace core\lib;

class Helper {

    /**
     *
     * @param array $array            
     * @return multitype:unknown
     */
    public static function removeEmptyArray($array) {
        $count = count($array);
        $new = [];
        for ($i = 0; $i < $count; $i ++) {
            if (trim($array[$i]) != '') {
                $new[] = $array[$i];
            }
        }
        return $new;
    }

    /**
     *
     * @param array $array            
     * @return multitype:unknown
     */
    public static function removeNullArray($array) {
        $count = count($array);
        $new = [];
        for ($i = 0; $i < $count; $i ++) {
            if ($array[$i] != null) {
                $new[] = array_filter($array[$i], 'strlen');
            }
        }
        return $new;
    }

    /**
     *
     * @param array $array            
     * @return string
     */
    public static function convert2Json($array, $header = true) {
        if ($array === false || $array == null) {
            $array = [];
        }
        if ($header)
            header('Content-type: application/json');
        return json_encode($array);
    }

    /**
     *
     * @param array $array            
     * @param string $glue            
     * @return void string
     */
    public static function convert2String($array, $glue = '|') {
        if ($array == null)
            return;
        $ret_str = self::Merger($array, $glue);
        if (strrpos($ret_str, $glue) != strlen($glue))
            $ret_str = substr($ret_str, 0, - (strlen($glue)));
        return $ret_str;
    }

    /**
     *
     * @param array $array            
     * @param string $glue            
     * @return string
     */
    private static function Merger($array, $glue = '|') {
        $ret_str = '';
        foreach ($array as $a) {
            $ret_str .= (is_array($a)) ? self::Merger($a, $glue) : strval($a) . $glue;
        }
        
        return $ret_str;
    }

    public static function initUpload($fileElementName, $fileSize, $exten = false) {
        if (! isset($_FILES[$fileElementName]))
            self::printLast(Messages::get('FileUpload_Nofile'));
        
        if (($_FILES[$fileElementName]['error'] != 0)) {
            switch ($_FILES[$fileElementName]['error']) {
                
                case '1':
                    $error = Messages::get('FileUpload_MaxPHP');
                    break;
                case '2':
                    $error = Messages::get('FileUpload_HTML');
                    break;
                case '3':
                    $error = Messages::get('FileUpload_Partially');
                    break;
                case '4':
                    $error = Messages::get('FileUpload_Nofile');
                    break;
                
                case '6':
                    $error = Messages::get('FileUpload_MissingTemp');
                    break;
                case '7':
                    $error = Messages::get('FileUpload_WriteDisk');
                    break;
                case '8':
                    $error = Messages::get('FileUpload_BadExtention');
                    break;
                case '999':
                default:
                    $error = Messages::get('FileUpload_NoCode');
            }
            self::printLast($error);
        } elseif (empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none') {
            self::printLast(Messages::get('FileUpload_Nofile'));
        } else {
            $original_name = $_FILES[$fileElementName]['name'];
            $name = Security::CleanUploadsChar($_FILES[$fileElementName]['name']);
            $ext = strtolower(substr($name, strlen($name) - 3, 3));
            if ($ext == null || trim($ext) == '') {
                self::printLast(Messages::get('NoPermission'));
            }
            
            $size = $_FILES[$fileElementName]['size'] / 1024;
            
            if ($size > $fileSize) {
                self::printLast(Messages::get('MaxSizeFile'));
            }
            
            if ($exten !== false) {
                
                if (is_array($exten)) {
                    if (! in_array($ext, $exten))
                        self::printLast(Messages::get('FileUpload_BadExtention'));
                } else 
                    if ($exten != $ext) {
                        self::printLast(Messages::get('FileUpload_BadExtention'));
                    }
            }
            return ['file' => $_FILES[$fileElementName]['tmp_name'],'name' => $name,'ext' => $ext];
        }
    }

    public static function initMultiUpload($fileElementName, $fileSize, $exten, $max) {
        if (! isset($_FILES[$fileElementName]) || ! is_array($_FILES[$fileElementName]['name']))
            self::printLast(Messages::get('FileUpload_Nofile'));
        $count = count($_FILES[$fileElementName]['name']);
        $files = [];
        if ($count > $max) {
            self::printLast(Messages::get('MaxSizeFile'));
        }
        for ($i = 0; $i < $count; $i ++) {
            if (($_FILES[$fileElementName]['error'][$i] != 0)) {
                switch ($_FILES[$fileElementName]['error'][$i]) {
                    case '1':
                        $error = Messages::get('FileUpload_MaxPHP');
                        break;
                    case '2':
                        $error = Messages::get('FileUpload_HTML');
                        break;
                    case '3':
                        $error = Messages::get('FileUpload_Partially');
                        break;
                    case '4':
                        $error = Messages::get('FileUpload_Nofile');
                        break;
                    
                    case '6':
                        $error = Messages::get('FileUpload_MissingTemp');
                        break;
                    case '7':
                        $error = Messages::get('FileUpload_WriteDisk');
                        break;
                    case '8':
                        $error = Messages::get('FileUpload_BadExtention');
                        break;
                    case '999':
                    default:
                        $error = Messages::get('FileUpload_NoCode');
                }
                self::printLast($error);
            } elseif (empty($_FILES[$fileElementName]['tmp_name'][$i]) || $_FILES[$fileElementName]['tmp_name'][$i] == 'none') {
                self::printLast(Messages::get('FileUpload_Nofile'));
            } else {
                $original_name = $_FILES[$fileElementName]['name'][$i];
                $name = Security::CleanUploadsChar($_FILES[$fileElementName]['name'][$i]);
                $ext = strtolower(substr($name, strlen($name) - 3, 3));
                if ($ext == null || trim($ext) == '') {
                    self::printLast(Messages::get('NoPermission'));
                }
                
                $size = $_FILES[$fileElementName]['size'][$i] / 1024;
                
                if ($size > $fileSize) {
                    self::printLast(Messages::get('MaxSizeFile'));
                }
                
                if ($exten !== false) {
                    
                    if (is_array($exten)) {
                        if (! in_array($ext, $exten))
                            self::printLast(Messages::get('FileUpload_BadExtention'));
                    } else 
                        if ($exten != $ext) {
                            self::printLast(Messages::get('FileUpload_BadExtention'));
                        }
                }
                $files[] = ['file' => $_FILES[$fileElementName]['tmp_name'][$i],'name' => $name,'ext' => $ext];
            }
        }
        return $files;
    }

    /**
     *
     * @param string $string            
     * @return boolean
     */
    public static function is_utf8($string) {
        return (mb_detect_encoding($string, 'UTF-8', true) == 'UTF-8');
    }

    public static function GetValidJson($key, $object, $value = NULL, $type = 2, $require = false) {
        if (isset($object->$key) && Security::CheckType($object->$key, $type))
            return $object->$key;
        else {
            if ($require === true)
                self::printLast(Messages::get('checkinput'));
            else
                return $value;
        }
    }

    function CheckTime($time1, $time2) {
        $start = strtotime($time1);
        $end = strtotime($time2);
        if ($end - $start > 0)
            return TRUE;
        else
            return FALSE;
    }

    /**
     *
     * @param int $Code            
     * @param string $text            
     * @return void
     */
    public static function raiseError($code, $text = '') {
        switch ($code) {
            case 400:
                $msg = '<title>400 Bad Request</title><h1>Bad Request</h1><p>Your browser sent a request that this server could not understand.<br></p>';
                self::printLast($msg, 400);
            case 404:
                $msg = '<title>404 Not Found</title><h1>Not Found</h1><p>The requested URL ' . $text . ' was not found on this server.</p>';
                self::printLast($msg, 404);
            case 403:
                $msg = '<title>403 Forbidden</title><h1>Forbidden</h1><p>You don\'t have permission to access ' . $text . ' this server.</p>';
                self::printLast($msg, 404);
        }
        exit();
    }

    public static function redirect($url = '', $append = '') {
        if ($url == '') {
            $url = __Defualt_Controller__;
        }
        $url = self::site_url($url);
        header('location: ' . $url . $append);
        exit();
    }

    public static function site_url($url = '') {
        if (parse_url($url, PHP_URL_SCHEME) != '')
            return $url;
        $protocol = ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $siteURL = $protocol . $_SERVER['HTTP_HOST'] . '/' . __Dynamic_PATH__;
        $url = str_ireplace(__Dynamic_PATH__, '', $url);
        $url = ltrim($url, '/');
        return $siteURL . $url;
    }

    public static function printLast($msg, $responseCode = 500) {
        http_response_code($responseCode);
        echo $msg;
        die();
    }
    function getallheaders() {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
