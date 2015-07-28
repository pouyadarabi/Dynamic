<?php
namespace core\lib;
use core\main\libs;
class Authorization extends libs
{
    public static function DoAuthorization()
    {        
        $config = Config::getAll();
  		$Perms = Session::get('Perms');
  		if ( empty( $Perms ) ){
  			Session::__setArray( 'Perms', $config['DefualtPerm'] );
  			$Perms = array($config['DefualtPerm']);
  		}
  		
  		$REQUEST_METHOD =  strtolower($_SERVER[ 'REQUEST_METHOD' ]);
  		$forbidden = true;
  		$ClassPerm = Annotations::getClassAnnotations( '\\app\\controller\\'.__REQ__CLASS__ );
  		$MethodPerm = Annotations::getMethodAnnotations( '\\app\\controller\\'.__REQ__CLASS__, __REQ__METHOD__ );
  		 
  		if(isset( $ClassPerm[ 'perm' ] ) && is_array($ClassPerm[ 'perm' ])) {
  			$forbidden = false;
  		}
	
  		if ( !isset( $MethodPerm[ 'perm' ] ) || !is_array( $MethodPerm[ 'perm' ] ) ) {
  			if ($forbidden && $config[ 'ForbiddenByDefault' ] ) {
  				Helper::RaiseError( 404 , $_SERVER['REQUEST_URI'] );
  			}
  		}
  		self::CheckAnnotations($ClassPerm, $Perms, $REQUEST_METHOD,$config);
  		self::CheckAnnotations($MethodPerm, $Perms, $REQUEST_METHOD,$config);
    }
    public static function CheckPermissionByNames($names)
    {
    	$perms = Session::get('Perms');
        if ($perms)
            if(is_array($names)) {
                foreach ($names as $perm)
                    if (in_array($perm, $perms))
                        return true;
            }else{
                if (in_array($names, $perms))
                    return true;
            }
        return false;
    }

	private static function CheckAnnotations($array,$Perms,$REQUEST_METHOD,$config){
	    if (! $array)
            return;
        if (isset($array['method']) && ! in_array($REQUEST_METHOD, $array['method']))
            Helper::RaiseError(404, $_SERVER['REQUEST_URI']);
        if (! isset($array['ignore']) || ! in_array('csrf', $array['ignore'])) { 
            if (in_array($config['CheckCsrfOnPerm'], $Perms) && $config['AntiCsrf'] == true) {               
                $Csrf_Method = explode(',', $config['CsrfCheckMethods']);
                if (in_array($REQUEST_METHOD, $Csrf_Method)) {                   
                    Security::CsrfTokenChecker($config['CsrfTokenLocation'], $config['CsrfTokenName']);
                }
            }
        }
	
		$MethodPerm = isset($array[ 'perm' ]) ? $array[ 'perm' ] : false;
		if ( $MethodPerm ) {
			foreach ( $MethodPerm as $Method ) {		
				if ( empty( $Perms ) )
					Helper::RaiseError( 404, $_SERVER['REQUEST_URI'] );
		
				if ( in_array( $Method, $Perms ) === TRUE )
					return;
			}	
			Helper::RaiseError( 404 , $_SERVER['REQUEST_URI']);
		}
		
	}
} 
