<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$account_id = get_account_id();
if(!$account_id)
{
	$sys->denied();
}
$form   = _class('params');
$params = array(
	'title'       => 'My Profile',
	'table'       => 'bbc_account',
	'config_pre'  => array(),
	'config'      => user_field($user->id),
	'config_post' => array(),
	'pre_func'    => '_is_email_unique',
	'post_func'   => '_user_change',
	'name'        => 'params',
	'id'          => $account_id
	);

$params['config_pre'] = array(
	'username' => array(
		'text' => 'Username',
		'type' => 'plain'
		),
	'name' => array(
		'text'      => 'Name',
		'type'      => 'text',
		'mandatory' => '1'
		)
	);
$params['config_post'] = array(
	'email' => array(
		'text' => 'Email',
		'type' => 'plain'
		),
	'vcode' => array(
		'text' => 'Validation Code',
		'type' => 'captcha'
		)
	);
$form->set($params);
$form->set_encode(false);
echo '<h1>'.lang('My Profile').'</h1>';
echo $form->show();

function _is_email_unique($form)
{
	if($form->is_updated)
	{

	}
}
function _user_change($form)
{
	global $user;
	user_call_func('user_change', $user->id);
}