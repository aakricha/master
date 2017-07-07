<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (function_exists('ioncube_loader_version'))
{
	$i = intval(ioncube_loader_version());
	if ($i < 6)
	{
		require_once __DIR__.'/8.php';
	}else{
		require_once __DIR__.'/9.php';
	}
}else{
	echo '<a href="https://www.ioncube.com/loader-wizard/loader-wizard.zip">click here</a> to get loader';
}