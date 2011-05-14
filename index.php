<?php
define('ROOT_DIR', dirname(__FILE__));
define('INCLUDE_DIR', ROOT_DIR . '/include');
define('PLUGINS_DIR', ROOT_DIR . '/plugins');
define('CACHE_DIR', ROOT_DIR . '/cache');
define('PLUGINS_INSTALL_DIR', ROOT_DIR . '/cache/plugins');
include 'include/Global.php';
Plugins::Run();