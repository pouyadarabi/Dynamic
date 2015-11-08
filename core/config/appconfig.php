<?php
return array (
		
		// ------------------------| Config |--------------------------- #
		'DebugMode' => false, // set it to false when you want publish application
		'showSqlErrors' => false,
		
		// --------------------------| DB |----------------------------- #
		'DbName' => 'DB_NAME', // Db Name
		'DbHost' => 'DB_HOST', // Db Host
		'DbUser' => 'DB_USER', // Db UserName
		'DbPass' => 'DB_PASS', // Db PassWord
		                       
		// ----------------------| Security |----------------------- #
		'Blowfish_Pre' => '$6$rounds=5000$', // blowfish for CRYPT_SHA256 encryption (php.net/manual/en/function.crypt.php)
		'Blowfish_End' => '$', // blowfish for encryption
		'UrlAllowedChars' => 'a-z 0-9~%.:_\-=&', // Allowed chars in url
		'Session_IPCheck' => false,
		'Session_UserAgentCheck' => false,
		'Session_Secure' => false, // check user agent and ip ,set it to true when you don't use cli
        'AllowedHosts' => '', // allowed hosts split by comma 
		// ----------------------| Config |----------------------- #
		
		'AppName' => 'Dynamic', // application name (used in cookie name)
		'Lang' => 'en', // use for messages 
		                        
		// Whilte List Controllers
		
		'Controllers' => [ 'main' ],
		
		// / Method Annotations
		'CheckAnnotations' => false,
		'ForbiddenByDefault' => true,
		'DefualtPerm' => 'guest',
		
		'AntiCsrf' => false,
		'CsrfCheckMethods' => 'post', // all , post , get , put , ....
		'CsrfTokenName' => '__pctk',
		'CsrfTokenLocation' => 'h', // h => header , p => post , g => get , r => request
		'CheckCsrfOnPerm' => 'logined',        
        
        // ----------------------| Route |----------------------- #
        'DefualtController' => 'main',
        'DefualtAction' => 'index',      
        
);
