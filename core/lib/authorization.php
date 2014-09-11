<?php
namespace core\lib;
use core\main\libs;
class Authorization extends libs
{
    public static function DoAuthorization()
    {
    	
        $Perms = Session::get('Perms');
        
        if ( empty( $Perms ) )
        	Session::__setArray( 'Perms', $GLOBALS['CONFIG']['DefualtPerm'] );
        $MethodPerm = Annotations::getMethodAnnotations( 'controller_' . __REQ__CLASS__, __REQ__METHOD__ );
        if ( !isset( $MethodPerm[ 'ignore' ] ) || !in_array( 'csrf', $MethodPerm[ 'ignore' ] ) ) {
            if ( isset( $Perms[ 'logined' ] ) && strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == 'post' ) {
                if ( $GLOBALS['CONFIG'][ 'AntiCsrf' ] === TRUE ) {
                    Session::CsrfTokenChecker( $GLOBALS['CONFIG'][ 'CsrfTokenName' ] );
                }
            }
        }
        if ( !isset( $MethodPerm[ 'perm' ] ) || !is_array( $MethodPerm[ 'perm' ] ) ) {
            if ( $GLOBALS['CONFIG'][ 'ForbiddenByDefault' ] ) {
                Helper::RaiseError( 404 , $_SERVER['REQUEST_URI'] );
            }
        }

        if ( isset( $MethodPerm[ 'method' ] ) && strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) != strtolower( $MethodPerm[ 'method' ][ 0 ] ) )
            Helper::RaiseError( 404, $_SERVER['REQUEST_URI'] );

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
    public static function CheckPermissionByNames($name)
    {
    	$perms = Session::get('Perms');
    	if ( in_array( $name, $perms) )
    		return true;
    	return false;
    }
} 