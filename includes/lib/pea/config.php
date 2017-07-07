<?php
if ( ! defined('_ROOT'))
{
	include dirname(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))))).'/config.php';
}
if ( ! defined('_ADMIN'))
{
	define('_ADMIN', '');
}
$path = 'includes/lib/pea/';
define( '_LAYOUT_DIR', _URL._ADMIN );
define ('_PEA_ROOT', _ROOT.$path );
define ('_PEA_URL', _URL.$path );
