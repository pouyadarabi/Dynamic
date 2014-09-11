<?php
function __autoload($class) {
	if (class_exists ( $class, false )) {
		return true;
	}
 	require  __SITE_PATH__ . '/' . strtolower ( $class )  . '.php';
	return true;
}