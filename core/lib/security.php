<?php
namespace core\lib;

class Security {

    public static function CleanSQLString($str) {
        $value = strtr($str, ["\x00" => '\x00',"\n" => '\n',"\r" => '\r','\\' => '\\\\',"'" => "\'",'"' => '\"',"\x1a" => '\x1a']);
        $search = ["\\","\x00","\n","\r","'",'"',"\x1a"];
        $replace = ["\\\\","\\0","\\n","\\r","\'",'\"',"\\Z"];
        
        return str_replace($search, $replace, $value);
    }

    public static function CleanXssFromObjectHelper(&$Object) {
        $array = get_object_vars($Object);
        $array = self::CleanXssString($array);
        $Object = json_decode(json_encode($array));
        return $Object;
    }

    public static function CleanXssString($str) {
        if (is_array($str)) {
            
            array_walk_recursive($str, ['self','CleanXssStringHelper']);
            return $str;
        }
        return self::CleanXssStringHelper($str);
    }

    public static function CleanXssStringHelper(&$str) {
        if (! is_object($str))
            $str = strip_tags(htmlentities($str, ENT_QUOTES, 'utf-8'));
        else {
            $str = self::CleanXssFromObject($str);
        }
        return $str;
    }

    public static function CleanXssFromObject($array) {
        if (is_array($array)) {
            
            array_walk_recursive($array, ['self','CleanXssFromObjectHelper']);
            return $array;
        }
        return self::CleanXssFromObjectHelper($array);
    }

    public static function CleanXssFromJson($Json) {
        $obj = json_decode($Json);
        
        return self::CleanXssFromObject($obj);
    }

    public static function CleanNumArray($arr) {
        $res = [];
        foreach ($arr as $val) {
            
            if (! is_array($val) && trim($val) != '' && is_numeric($val)) {
                $res[] = $val;
            }
        }
        return $res;
    }

    public static function CleanUrlChar($page) {
        $filter = ['0','1','2','3','4','5','6','7','8','9','/','.','%','\\',":"];
        if (is_array($page)) {
            foreach ($page as $index => $value) {
                $page[$index] = str_replace($filter, '', $value);
            }
            return $page;
        }
        return str_replace($filter, '', $page);
    }

    public static function CleanUploadsChar($file) {
        $windowsReserved = ['CON','PRN','AUX','NUL','COM1','COM2','COM3','COM4','COM5','COM6','COM7','COM8','COM9','LPT1','LPT2','LPT3','LPT4','LPT5','LPT6','LPT7','LPT8','LPT9'];
        $badWinChars = array_merge(array_map('chr', range(0, 31)), array("<",">",":",'"',"/","\\","|","?","*"));
        if (is_array($file)) {
            foreach ($file as $index => $value) {
                $var = str_replace($badWinChars, '', $value);
                $file[$index] = str_replace($windowsReserved, '', $var);
            }
            return $file;
        }
        $file = str_replace($badWinChars, '', $file);
        $file = str_replace($windowsReserved, '', $file);
        
        return $file;
    }

    public static function HtmlDecode($str) {
        if (is_array($str)) {
            foreach ($str as $index => $value) {
                $str[$index] = html_entity_decode($value);
            }
            return $str;
        }
        return html_entity_decode($str);
    }

    public static function CheckInput($Name, $Method, $Type = 2) {
        if ($Method == 'p') {
            
            $str = (isset($_POST[$Name]) ? $_POST[$Name] : '');
        } else {
            $str = (isset($_GET[$Name]) ? $_GET[$Name] : '');
        }
        
        if (! is_array($str) && trim($str) == '') {
            return false;
        }
        
        if (self::CheckType($str, $Type) !== false) {
            return true;
        } else
            return false;
    }

    public static function CheckType($input, $Type) {
        if (! is_array($input) && $Type == 's')
            return self::TypeString($input);
        if (! is_array($input) && $Type == 'd')
            return self::TypeDate($input);
        if (! is_array($input) && $Type == 'i')
            return self::TypeInteger($input);
        if (! is_array($input) && $Type == 'e')
            return self::TypeEmail($input);
        if (! is_array($input) && $Type == 'j')
            return self::TypeJson($input);
        if (! is_array($input) && $Type == 'o')
            return self::TypeObject($input);
        if ($Type == 'oa')
            return self::TypeObjectArray($input);
        if ($Type == 'na')
            return self::TypeNumArray($input);
        if ($Type == 'sa')
            return self::TypeStringArray($input);
        if ($Type == 'a')
            return is_array($input);
        
        return false;
    }

    private static function TypeDate($str) {
        try {
            $dt = new \DateTime(trim($str));
        } catch (\Exception $e) {
            return false;
        }
        
        $month = $dt->format('m');
        $day = $dt->format('d');
        $year = $dt->format('Y');
        if (checkdate($month, $day, $year)) {
            return true;
        } else {
            return false;
        }
    }

    private static function TypeString($str) {
        return is_string($str);
    }

    private static function TypeInteger($id) {
        if (trim($id) != '' && is_numeric($id)) {
            return $id;
        } else {
            return false;
        }
    }

    private static function TypeEmail($str) {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    private static function TypeNumArray($arr) {
        if (! is_array($arr)) {
            return false;
        }
        foreach ($arr as $val) {
            if (trim($val) == '' || ! is_numeric($val)) {
                return false;
            }
        }
        return true;
    }

    private static function TypeStringArray($arr) {
        if (! is_array($arr)) {
            return false;
        }
        foreach ($arr as $val) {
            if (trim($val) == '' || ! is_string($val)) {
                return false;
            }
        }
        return true;
    }

    private static function TypeJson($str) {
        return json_decode($str) != null;
    }

    private static function TypeObject($str) {
        return is_object($str);
    }

    private static function TypeObjectArray($array) {
        if (! is_array($array)) {
            return false;
        }
        foreach ($array as $val) {
            if (! self::TypeObject($val)) {
                return false;
            }
        }
        
        return true;
    }

    public static function CleanArrayFilterInteger($ids) {
        if (count($ids) < 1) {
            return false;
        }
        $flag = true;
        foreach ($ids as $id) {
            
            if (self::CleanFilterInteger($id) === false) {
                $flag = false;
            }
        }
        return $flag;
    }

    public static function cleanFileName($file) {
        $badChars = array_merge(array_map('chr', range(0, 31)), ["<",">",":",'"',"/","\\","|","?","*"]);
        if (is_array($file)) {
            foreach ($file as $index => $value) {
                $file[$index] = str_replace($badChars, '', $value);
            }
            return $file;
        }
        $file = str_replace($badChars, '', $file);
        return $file;
    }

    public static function CsrfTokenGenerator() {
        return md5('$%*DynamicT0ken@#!') . substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789", 10)), 0, 10);
    }

    public static function CsrfTokenChecker($location = 'p', $input = '__pctk') {
        $array = [];
        
        switch ($location) {
            case 'r':
                $array = $_POST;
                break;
            case 'g':
                $array = $_GET;
                break;
            case 'h':
                if (! function_exists('getallheaders')) {
                    $array = Helper::getallheaders();
                } else {
                    $array = getallheaders();
                }
                break;
            default:
                $array = $_POST;
                break;
        }
        
        if (! isset($array[$input]) || trim($array[$input]) == '' || $_SESSION['Token'] != $array[$input]) {
            die(Messages::get('checkinput'));
        }
    }

    public static function MyCrypt($passwd, &$salt = '') {
        if (trim($salt) == '') {
            $Allowed_Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
            $Chars_Len = 63;
            $Salt_Length = 21;
            $salt = "";
            for ($i = 0; $i < $Salt_Length; $i ++) {
                $salt .= $Allowed_Chars[mt_rand(0, $Chars_Len)];
            }
        }
        $config = Config::getAll();
        $bcrypt_salt = $config['Blowfish_Pre'] . $salt . $config['Blowfish_End'];
        
        $hashed_password = crypt($passwd, $bcrypt_salt);
        return $hashed_password;
    }
}