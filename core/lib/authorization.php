<?php
namespace core\lib;

class Authorization
{
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
} 
