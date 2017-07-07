<?php
$formName		= $_GET['formName'];
$formType		= $_GET['formType'];
$reportType	= $_GET['reportType'];

$file	= 'php'. ucfirst(strtolower($formType)) . ucfirst(strtolower($reportType)) . '.php';
if (!preg_match('~/~is', $file))
{
	include_once( $file );

	$class= 'php'.$formType.$reportType;
	$obj	= new $class();
	if (!empty($_GET['name']))
	{
		if (!defined('_CACHE'))
		{
			define('_CACHE', _ROOT.'images/cache/');
		}
		$name = str_replace('/', '', $_GET['name']); // pengamanan agar tidak membaca file diluar cache
		$file = _CACHE.implode('/', str_split($name, 2)).'.cfg';
		$json = json_decode(phpReportRead($file));
	}else{
		$json = $_GET;
	}
	foreach($json AS $var => $val)
	{
		$obj->$var = $val;
	}
	$obj->write();
}

function phpReportRead($file = '', $method = 'r')
{
	if ( empty($file) || !file_exists($file))
	{
		return FALSE;
	}
	if (function_exists('file_get_contents'))
	{
		return file_get_contents($file);
	}
	if ( ! $fp = @fopen($file, $method))
	{
		return FALSE;
	}
	flock($fp, LOCK_SH);
	$data = '';
	if (filesize($file) > 0)
	{
		$data =& fread($fp, filesize($file));
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	return $data;
}
