<?php
return [
    'DebugMode' => false,
    'showSqlErrors' => false,
    
    'DbName' => 'DB_NAME',
    'DbHost' => 'DB_HOST',
    'DbUser' => 'DB_USER',
    'DbPass' => 'DB_PASS',
    
    'Blowfish_Pre' => '$6$rounds=5000$',
    'Blowfish_End' => '$',
    'UrlAllowedChars' => 'a-z 0-9~%.:_\-=&',
    'Session_IPCheck' => false,
    'Session_UserAgentCheck' => false,
    'Session_Secure' => false,
    
    'AllowedHosts' => '',
    
    'AppName' => 'Dynamic',
    'Lang' => 'en',

    'URLMapping' => false,
    'Controllers' => ['main'],

    'CheckAnnotations' => false,
    'ForbiddenByDefault' => true,
    'DefualtPerm' => 'guest',

    'AntiCsrf' => false,
    'CsrfCheckMethods' => 'post',
    'CsrfTokenName' => '__pctk',
    'CsrfTokenLocation' => 'h',
    'CheckCsrfOnPerm' => 'logined',
    
    'AntiClickJacking' => true,
    
    'DefualtController' => 'main',
    'DefualtAction' => 'index',
    'NotFoundController' => 'notfound'
    
];
