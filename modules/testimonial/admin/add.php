<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _class('params');
$q = "SELECT * FROM testimonial_field WHERE active=1 ORDER BY orderby";
$params = array(
	'title'				=> 'Add Testimonial'
,	'table'				=> 'testimonial'
,	'config_pre'	=> array()
,	'config'			=> $db->getAll($q)
,	'config_post'	=> array()
,	'name'				=> 'params'
,	'id'					=> 0
,	'post_func'		=> '_change_date'
);

$params['config_pre'] = array(
	'name'	=> array(
						'text'	=> 'Name'
					,	'type'	=> 'text'
					,	'attr'	=> 'size="30"'
					,	'mandatory'	=> 1
					)
);
$params['config_post'] = array(
	'email'	=> array(
						'text'	=> 'Email'
					,	'type'	=> 'text'
					,	'attr'	=> 'size="30"'
					,	'mandatory'	=> 1
					)
,	'message'	=> array(
						'text'	=> 'Message'
					,	'type'	=> 'textarea'
					,	'attr'	=> 'rows="5" cols="50"'
					,	'mandatory'	=> 1
					)
);
$form->set($params);
$form->set_encode(false);
echo $form->show();

function _change_date($form)
{
	global $sys, $db;
	$conf = get_config('testimonial', 'testimonial');
	$q = "UPDATE $form->table SET `date`=NOW(), publish=".@intval($conf['approved'])." WHERE id=$form->table_id";
	$db->Execute($q);
}
