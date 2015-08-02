<?php
namespace core\lib;

class Authentication
{
    public static function SetPermissions( $PermissionNames )
    {
        $perms = Session::get('Perms');
        if(empty($perms)){
        	$perms = [];
        }
        if ( is_array( $PermissionNames ) ) {
            foreach ( $PermissionNames as $Perm ) {
                if ( !in_array( $Perm, $perms) )
                   Session::__setArray( 'Perms', $Perm );
            }
        } else {
            Session::__setArray( 'Perms', $PermissionNames );
        }
        if ( ( $key = array_search( Config::get('DefualtPerm'),$perms ) ) !== false ) {
            Session::__unsetArray('Perms',$key);
        }
    }

} 