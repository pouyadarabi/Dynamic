<?php
namespace core\lib;
use core\main\libs;
class Template extends libs
{

    private static $items;

    public static function set( $index, $value )
    {
        self::$items[ $index ] = $value;
    }
    public static function setArray(array $array )
    {
    	foreach ($array as $key => $value) {
    		self::$items[ $key ][] =  $value;
    	}
    	
    }
    public static function Show( $File, $Print = TRUE, $CheckPerm = TRUE )
    {
        $File = __View_PATH__ . $File;
        $output = file_get_contents( $File . '.html' );
        if(self::$items)
	        foreach ( self::$items as $item => $value ) {
	            $output = str_replace( '[@' . $item . ']', $value, $output );
	        }
        preg_match_all( '/\[@Include_(.*?)\]/', $output, $Array );
        if ( count( $Array ) > 0 ) {
            $Incs = $Array[ 1 ];
            foreach ( $Incs as $name ) {
                $Cleanedname = Security::CleanDownloadChar( $name );
                $namearray = explode( '/', $Cleanedname );

                $Cleanedname = str_replace( '_', '/', $Cleanedname );
                $Included = self::Show( $Cleanedname, FALSE );

                $str = '[@Include_' . $name . ']';
                $output = str_replace( $str, $Included, $output );
            }
        }
        if ( $CheckPerm === TRUE ) {
            preg_match_all( '/\[#(.*?)\]/', $output, $PermArray );
            $Incs = $PermArray[ 1 ];
            $count = count( $Incs );

            if ( $count > 0 ) {
                for ( $i = 0; $i < $count; $i++ ) {
                    $name = $Incs[ $i ];
                    if ( substr( $name, 0, 1 ) == '/' || strpos( $output, "[#$name]" ) === false )
                        continue;
                    $arr = explode( '||', $name );
                    if ( Authorization::CheckPermissionByNames( $arr ) ) {
                        $name = str_replace( '|', '\|', $name );
                        $output = preg_replace( "/\[#(.|\/)*?$name\]/", '', $output );
                    } else {

                        $output = substr( $output, 0, strpos( $output, "[#$name]" ) ) . substr( $output, strpos( $output, "[#/$name]" ) + strlen( "[#/$name]" ), strlen( $output ) );
                    }
                }
            }
            unset( $PermArray );
            preg_match_all( '/\[\$(.*?)\]/', $output, $PermArray );
            $Incs = $PermArray[ 1 ];
            if ( count( $Incs ) > 0 ) {
                foreach ( $Incs as $name ) {
                    $arr = explode( '||', $name );
                    if ( Authorization::CheckPermissionByNames( $arr ) ) {
                        $name = str_replace( '|', '\|', $name );
                        $output = preg_replace( '/\[\$' . $name . '\]/', '', $output );
                    } else {
                        $output = str_replace( '[$' . $name . ']', 'disabled', $output );
                    }
                }
            }
        }
        $output = preg_replace( '/\[@(.*?)\]/', '', $output );
        if ( $Print === TRUE )
            echo $output;
        else
            return $output;
    }
}