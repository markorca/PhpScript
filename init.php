<?php
date_default_timezone_set('UTC');
if (!defined('SCRIPT_NAME')) {
    if (isset($GLOBALS['argv'][2]))
        define('SCRIPT_NAME', basename($GLOBALS['argv'][2], '.php'));
}
define('ENV_FILE', 'config/env.ini');
define('CONFIG_FILE', 'config/config.ini');