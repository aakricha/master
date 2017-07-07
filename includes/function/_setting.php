<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
$_setting = array(
	'characters'=> array(
		'text'		=> 'Invalid Char'
	,	'tips'		=> 'Enter the characters you want to ban (messages that contain them will not be sent'
	,	'type'		=> 'text'
	,	'attr'		=> ' size="40"'
	, 'default'	=> '< &lt; &gt; href sexyasian redtube.com'
	)
,	'textarea'		=> array(
		'tips'		=> 'Period to refresh in micro second (leave it blank to disable auto refresh)'
	,	'type'		=> 'textarea'
	, 'default'	=> 'sfdghgfhg'
	)
,	'select'		=> array(
		'tips'		=> 'Period to refresh in micro second (leave it blank to disable auto refresh)'
	,	'type'		=> 'select'
	, 'option'	=> array('yes', 'no')
	, 'default'	=> 'no'
	)
,	'radio'		=> array(
		'tips'		=> 'Period to refresh in micro second (leave it blank to disable auto refresh)'
	,	'type'		=> 'radio'
	,	'delim'		=> "<br />\n"
	, 'option'	=> array('yes', 'no')
	, 'default'	=> '0'
	)
,	'checkbox'		=> array(
		'tips'		=> 'Period to refresh in micro second (leave it blank to disable auto refresh)'
	,	'type'		=> 'checkbox'
	,	'delim'		=> "<br />\n"
	, 'option'	=> array('yes', 'no')
	, 'default'	=> array(1,0)
	)
,	'checkbox2'		=> array(
		'tips'		=> 'Period to refresh in micro second (leave it blank to disable auto refresh)'
	,	'type'		=> 'checkbox'
	,	'option'	=> 'activate'
	, 'default'	=> 1
	)
,	'file'		=> array(
		'tips'		=> 'Period to refresh in micro second (leave it blank to disable auto refresh)'
	,	'type'		=> 'file'
	, 'default'	=> 'sfdghgfhg'
	, 'path'		=> 'images/Images/'
	)
);
#*/
function _setting($arr, $config = array(), $form_title = 'Additional Parameter', $name = 'config')
{
	foreach ($arr as $key => $value)
	{
		if (!empty($value['help']) && empty($value['tips']))
		{
			$arr[$key]['tips'] = $value['help'];
			unset($arr[$key]['help']);
		}
	}
	$c = _class('bbcconfig', $arr, $name, '', $form_title);
	$c->default = $config;
	$c->show_param($c->config, $c->default, $c->title, $c->name);
}
