<?php
namespace core\lib;

class Template {

    private static $items;

    private static $checkPerms;

    private static $viewFile;

    public static function set($index, $value, $view_name = 'dynamic_global') {
        self::$items[$view_name][$index] = $value;
    }

    public static function setArray(array $array, $view_name = 'dynamic_global') {
        foreach ($array as $key => $value) {
            self::$items[$view_name][$key] = $value;
        }
    }

    public static function Show($viewFile, $return = false, $checkPerm = true) {
        self::$checkPerms = $checkPerm;
        self::$viewFile = $viewFile;
        self::checkVars();
        $output = self::parseInput();
        $output = self::parsePermissions($output);
        $output = self::parseIncludes($output);
        $output = self::parseConditions($output);
        $output = self::parseLoops($output);
        $output = self::parseVariables($output);
        $output = self::removeUnused($output);
        
        if ($return)
            return $output;
        else
            echo $output;
    }

    private static function checkVars() {
        if (! isset(self::$items[self::$viewFile]))
            self::$items[self::$viewFile] = [];
        if (! isset(self::$items['dynamic_global']))
            self::$items['dynamic_global'] = [];
    }

    private static function parseInput() {
        $viewFile = __View_PATH__ . self::$viewFile;
        $output = file_get_contents($viewFile . '.html');
        return $output;
    }

    private static function parseVariables($input) {
        $output = $input;
        $items = array_merge(self::$items[self::$viewFile], self::$items['dynamic_global']);
        
        if ($items) {
            foreach ($items as $item => $value) {
                $output = str_replace('[@' . $item . ']', $value, $output);
            }
        }
        
        return $output;
    }

    private static function parseIncludes($input) {
        $output = $input;
        preg_match_all('/\[include\s(.*?)\]/', $output, $Array);
        
        if (count($Array) > 0) {
            $Incs = $Array[1];
            
            foreach ($Incs as $name) {
                
                $Included = self::Show($name, true);
                
                $str = '[include ' . $name . ']';
                $output = str_replace($str, $Included, $output);
            }
        }
        return $output;
    }

    private static function parsePermissions($input) {
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

    private static function parseConditions($input) {
        $conditions = null;
        preg_match_all('/\[if\s(.*?)\]/', $input, $conditions);
        
        $conditions = $conditions[1];
        
        if ($conditions) {
            foreach ($conditions as $condition) {
                $items = array_merge(self::$items[self::$viewFile], self::$items['dynamic_global']);
                if (isset($items[$condition]) && $items[$condition]) {
                    
                    $len = strlen("[if $condition]");
                    $condition_s = strpos($input, "[if $condition]");
                    $condition_e = strpos($input, "[/if]", $condition_s);
                    
                    $input = substr($input, 0, strpos($input, "[/if]")) . substr($input, strpos($input, "[/if]") + 5, strlen($input));
                    
                    $input = substr($input, 0, $condition_s) . substr($input, $condition_s + $len, strlen($input));
                } else {
                    $input = substr($input, 0, strpos($input, "[if $condition]")) . substr($input, strpos($input, "[/if]") + 5, strlen($input));
                }
            }
        }
        
        return $input;
    }

    private static function parseLoops($input) {
        $loops = null;
        preg_match_all('/\[for\s(.*?)\]/', $input, $loops);
        
        $loops = $loops[1];
        
        if ($loops) {
            foreach ($loops as $loop) {
                $items = array_merge(self::$items[self::$viewFile], self::$items['dynamic_global']);
                if (isset($items[$loop])) {
                    $vars = $items[$loop];
                    $loop_s = strpos($input, "[for $loop]") + strlen("[for $loop]");
                    $loop_e = strpos($input, "[/for]");
                    $loop_body = substr($input, $loop_s, $loop_e - $loop_s);
                    $body = '';
                    $num = 1;
                    
                    foreach ($vars as $var) {
                        $replaced = $loop_body;
                        foreach ($var as $item => $value) {
                            $replaced = str_replace('[@' . $item . ']', $value, $replaced);
                        }
                        $replaced = str_replace('[@_num]', $num ++, $replaced);
                        $body .= $replaced;
                    }
                    $input = substr_replace($input, $body, $loop_e + 6, 0);
                }
                $input = substr($input, 0, strpos($input, "[for $loop]")) . substr($input, strpos($input, "[/for]") + 6, strlen($input));
            }
        }
        
        return $input;
    }

    private static function removeUnused($input) {
        $output = preg_replace('/\[(@|\$|#)(.*?)\]/', '', $input);
        return $output;
    }
}
