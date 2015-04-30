<?php
namespace core\lib;
use core\main\libs;

class Template extends libs {

    private static $items;

    private static $checkPerms;

    public static function set ($index, $value) {
        self::$items[$index] = $value;
    }

    public static function setArray (array $array) {
        foreach ($array as $key => $value) {
            self::$items[$key] = $value;
        }
    }

    public static function Show ($viewFile, $return = false, $checkPerm = true) {
        self::$checkPerms = $checkPerm;
        
        $output = self::parseInput($viewFile);
        $output = self::parsePermissions($output);
        $output = self::parseIncludes($output);
        $output = self::parseVariables($output);
        $output = self::parseLoops($output);
        $output = self::removeUnused($output);
      
        if ($return)
            return $output;
        else
            echo $output;
    }

    private static function parseInput ($viewFile) {
        $viewFile = Security::cleanFileName($viewFile);
        $viewFile = __View_PATH__ . $viewFile;
        $output = file_get_contents($viewFile . '.html');
        return $output;
    }

    private static function parseVariables ($input) {
        $output = $input;
        if (self::$items) {
            foreach (self::$items as $item => $value) {
                $output = str_replace('[@' . $item . ']', $value, $output);
            }
        }
        
        return $output;
    }

    private static function parseIncludes ($input) {
        $output = $input;
        preg_match_all('/\[@Include_(.*?)\]/', $output, $Array);
        if (count($Array) > 0) {
            $Incs = $Array[1];
            foreach ($Incs as $name) {
                $Cleanedname = Security::CleanDownloadChar($name);
                $namearray = explode('/', $Cleanedname);
                
                $Cleanedname = str_replace('_', '/', $Cleanedname);
                $Included = self::Show($Cleanedname, false);
                
                $str = '[@Include_' . $name . ']';
                $output = str_replace($str, $Included, $output);
            }
        }
        return $output;
    }

    private static function parsePermissions ($input) {
        $output = $input;
        if (! self::$checkPerms)
            return $output;
        preg_match_all('/\[#(.*?)\]/', $output, $PermArray);
        $Incs = $PermArray[1];
        $count = count($Incs);
        
        if ($count > 0) {
            for ($i = 0; $i < $count; $i ++) {
                $name = $Incs[$i];
                if (substr($name, 0, 1) == '/' || strpos($output, "[#$name]") === false)
                    continue;
                $arr = explode('||', $name);
                if (Authorization::CheckPermissionByNames($arr)) {
                    $name = str_replace('|', '\|', $name);
                    $output = preg_replace("/\[#(.|\/)*?$name\]/", '', $output);
                } else {
                    
                    $output = substr($output, 0, strpos($output, "[#$name]")) . substr($output, strpos($output, "[#/$name]") + strlen("[#/$name]"), strlen($output));
                }
            }
        }
        unset($PermArray);
        preg_match_all('/\[\$(.*?)\]/', $output, $PermArray);
        $Incs = $PermArray[1];
        if (count($Incs) > 0) {
            foreach ($Incs as $name) {
                $arr = explode('||', $name);
                if (Authorization::CheckPermissionByNames($arr)) {
                    $name = str_replace('|', '\|', $name);
                    $output = preg_replace('/\[\$' . $name . '\]/', '', $output);
                } else {
                    $output = str_replace('[$' . $name . ']', 'disabled', $output);
                }
            }
        }
        return $output;
    }

    private static function parseLoops ($input) {
        $loops = null;
        preg_match_all('/\[for\s(.*?)\]/', $input, $loops);
        
        $loops = $loops[1];
        
        if ($loops) {
            foreach ($loops as $loop) {
                if (isset(self::$items[$loop])) {
                    $vars = self::$items[$loop];
                    $loop_s = strpos($input, "[for $loop]") + strlen("[for $loop]");
                    $loop_e = strpos($input, "[/for]");
                    $loop_body = substr($input, $loop_s, $loop_e - $loop_s);
                    $body = '';
                    
                    foreach ($vars as $var) {
                        $replaced = $loop_body;
                        foreach ($var as $item => $value) {
                            $replaced = str_replace('[@' . $item . ']', $value, $replaced);
                        }
                        
                        $input = substr_replace($input, $replaced, $loop_e + 6, 0);
                    }
                }
                $input = substr($input, 0, strpos($input, "[for $loop]")) . substr($input, strpos($input, "[/for]") + 6, strlen($input));
            }
        }
        
        return $input;
    }

    private static function removeUnused ($input) {
        $output = preg_replace('/\[(@|\$|#)(.*?)\]/', '', $input);
        return $output;
    }
}