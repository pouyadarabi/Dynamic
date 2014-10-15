<?php
namespace core\lib;
use core\main\libs;
class Authorization extends libs
{
    public static function DoAuthorization()
    {
        $Perms = Session::get('Perms');    
        $checkPerm =  $GLOBALS['CONFIG'][ 'CheckPermissions' ];
        
        if ( $checkPerm && empty( $Perms ) ){
        	Session::__setArray( 'Perms', $GLOBALS['CONFIG']['DefualtPerm'] );
        	$Perms = array($GLOBALS['CONFIG']['DefualtPerm']);
        }
      	$REQUEST_METHOD =  strtolower($_SERVER[ 'REQUEST_METHOD' ]);
        $MethodPerm = Annotations::getMethodAnnotations( '\\app\\controller\\'.__REQ__CLASS__, __REQ__METHOD__ );
    	if ( !isset( $MethodPerm[ 'ignore' ] ) || !in_array( 'csrf', $MethodPerm[ 'ignore' ] ) ) {
            if ( isset( $Perms[ 'logined' ] ) && $GLOBALS['CONFIG'][ 'AntiCsrf' ] === TRUE  ) {
            	$Csrf_Method = explode(',',$GLOBALS['CONFIG'][ 'CsrfCheckMethods' ]);
                if ( in_array(array('all',$REQUEST_METHOD), $Csrf_Method) ) {
                    Security::CsrfTokenChecker( $GLOBALS['CONFIG'][ 'CsrfTokenLocation' ],$GLOBALS['CONFIG'][ 'CsrfTokenName' ] );
                }
            }
        }
      
        if ( $checkPerm && (!isset( $MethodPerm[ 'perm' ] ) || !is_array( $MethodPerm[ 'perm' ] )) ) {
            if ( $GLOBALS['CONFIG'][ 'ForbiddenByDefault' ] ) {
                Helper::RaiseError( 404 , $_SERVER['REQUEST_URI'] );
            }
        }


        if ( isset( $MethodPerm[ 'method' ] ) && !in_array($REQUEST_METHOD, $MethodPerm[ 'method' ] ) )
            Helper::RaiseError( 404, $_SERVER['REQUEST_URI'] );

        if($checkPerm){
	        $MethodPerm = $MethodPerm[ 'perm' ];
	
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
    public static function CheckPermissionByNames($name)
    {
    	$perms = Session::get('Perms');
    	if ( in_array( $name, $perms) )
    		return true;
    	return false;
    }
} 