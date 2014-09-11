<?php
use core\system\Application;
define ( '__SITE_PATH__', dirname( __FILE__ ) );
require_once __SITE_PATH__ . '/core/system/autoloader.php';

Application::initialize();