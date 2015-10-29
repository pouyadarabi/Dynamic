<?php
namespace core\system;
use core\lib\Config;
use core\lib\Session;
use core\lib\Annotations;
use core\lib\Helper;
use core\lib\Security;

class GateWay {

    public static function check ($class,$method) {
        self::CheckAnnotations($class,$method);
    }

    private static function checkAnnotations ($class,$method) {
        $config = Config::getAll();
        $Perms = Session::get('Perms');
        if (empty($Perms)) {
            Session::__setArray('Perms', $config['DefualtPerm']);
            $Perms = [$config['DefualtPerm']];
        }
        
        $forbidden = true;
        $ClassPerm = Annotations::getClassAnnotations('\\app\\controller\\' . $class);
        $MethodPerm = Annotations::getMethodAnnotations('\\app\\controller\\' . $class, $method);
        
        if (isset($ClassPerm['perm']) && is_array($ClassPerm['perm'])) {
            $forbidden = false;
        }
        
        if (! isset($MethodPerm['perm']) || ! is_array($MethodPerm['perm'])) {
            if ($forbidden && $config['ForbiddenByDefault']) {
                Helper::RaiseError(404, $_SERVER['REQUEST_URI']);
            }
        }
        $ignoreChecks = ['csrf' => false,'clickjacking' => false];
        self::CheckAnnotation($ClassPerm, $Perms, $config, $ignoreChecks);
        self::CheckAnnotation($MethodPerm, $Perms, $config, $ignoreChecks);
        
        if ($config['AntiClickJacking'] && ! $ignoreChecks['clickjacking']) {
            header('X-Frame-Options: SameOrigin');
        }
        if ($config['AntiCsrf'] && ! $ignoreChecks['csrf']) {
            if (in_array($config['CheckCsrfOnPerm'], $Perms)) {
                $Csrf_Method = explode(',', $config['CsrfCheckMethods']);
                if (in_array(__REQUEST_METHOD__, $Csrf_Method)) {
                    Security::CsrfTokenChecker($config['CsrfTokenLocation'], $config['CsrfTokenName']);
                }
            }
        }
    }

    private static function CheckAnnotation ($array, $Perms, $config, &$globalConf) {
        if (! $array)
            return;
        
        if (isset($array['ignore'])) {
            $ignores = $array['ignore'];
            if (in_array('csrf', $ignores)) {
                $globalConf['csrf'] = true;
            }
            if (in_array('clickjacking', $ignores)) {
                $globalConf['clickjacking'] = true;
            }
        }
        
        if (isset($array['method']) && ! in_array(__REQUEST_METHOD__, $array['method']))
            Helper::RaiseError(404, $_SERVER['REQUEST_URI']);
        
        
        $MethodPerm = isset($array['perm']) ? $array['perm'] : false;
        if ($MethodPerm) {
            foreach ($MethodPerm as $Method) {
                if (empty($Perms))
                    Helper::RaiseError(404, $_SERVER['REQUEST_URI']);
                
                if (in_array($Method, $Perms) === true)
                    return;
            }
            Helper::RaiseError(404, $_SERVER['REQUEST_URI']);
        }
    }
}