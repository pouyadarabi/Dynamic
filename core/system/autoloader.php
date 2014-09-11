<?php
function __autoload($class) {
	if (class_exists ( $class, false )) {
		return true;
	}
 	require  __SITE_PATH__ . '/' . str_replace('\\', '/', strtolower ( $class ))  . '.php';
	return true;
}