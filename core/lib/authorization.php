<?php
namespace core\lib;
use core\main\libs;
class Authorization extends libs
{
    public static function DoAuthorization()
    {        
        $config = Config::getAll();
        $checkPerm = $config[ 'CheckPermissions' ];
      	if ( $checkPerm ){
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
    }
    public static function CheckPermissionByNames($name)
    {
    	$perms = Session::get('Perms');
    	if ( in_array( $name, $perms) )
    		return true;
    	return false;
    }

	private static function CheckAnnotations($array,$Perms,$REQUEST_METHOD,$config){
		if(!$array)
			return ;
		if ( isset( $array[ 'method' ] ) && !in_array($REQUEST_METHOD, $array[ 'method' ] ) )
			Helper::RaiseError( 404, $_SERVER['REQUEST_URI'] );
		
		if ( !isset( $array[ 'ignore' ] ) || !in_array( 'csrf', $array[ 'ignore' ] ) ) {
			if ( isset( $Perms[ 'logined' ] ) && $config[ 'AntiCsrf' ] === TRUE  ) {
				$Csrf_Method = explode(',',$config[ 'CsrfCheckMethods' ]);
				if ( in_array(array('all',$REQUEST_METHOD), $Csrf_Method) ) {
					Security::CsrfTokenChecker($config[ 'CsrfTokenLocation' ],$config[ 'CsrfTokenName' ] );
				}
			}
		}
	
		$MethodPerm = $array[ 'perm' ];
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