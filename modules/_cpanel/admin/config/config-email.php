<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$_setting = array(
	'name'=> array(
		'text'		=> 'Name'
	,	'type'		=> 'text'
	,	'tips'		=> 'This is the global email name. If the email is sent from the website with unspecified sender\'s name then this field will be used.'
	,	'attr'		=> 'size="40"'
	)
,	'address'		=> array(
		'text'		=> 'Address'
	,	'type'		=> 'text'
	,	'tips'		=> 'This is the global email address. If the email is sent from the website with unspecified sender then this address is used.'
	,	'attr'		=> 'size="40"'
	)
,	'subject'		=> array(
		'text'		=> 'Subject'
	,	'tips'		=> 'This field is used as prefix in email\'s subject.'
	,	'type'		=> 'text'
	,	'attr'		=> 'size="40"'
	)
,	'footer'=> array(
		'text'		=> 'Footer'
	,	'tips'		=> 'This content will be placed at the end of email content.'
	,	'type'		=> 'textarea'
	,	'attr'		=> " cols=60 rows=5"
	)
);
$params = array(
  'config'=> $_setting
, 'name'	=> 'email'
, 'title'	=> 'Email Configuration'
, 'id'		=> 0
);
$conf->set($params);
