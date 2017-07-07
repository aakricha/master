<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _class('params');
echo '<h1>'.lang('Post Guestbook').'</h1>';
$q = "SELECT * FROM guestbook_field WHERE active=1 ORDER BY orderby";
$params = array(
	'title'				=> 'Use form below'
,	'table'				=> 'guestbook'
,	'config_pre'	=> array()
,	'config'			=> $db->getAll($q)
,	'config_post'	=> array()
,	'name'				=> 'params'
,	'id'					=> 0
,	'post_func'		=> '_send_mail'
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
,	'vcode'	=> array(
						'text'	=> 'Validation Code'
					,	'type'	=> 'captcha'
					)
);
$form->set($params);
$form->set_encode(false);
echo $form->show();

function _send_mail($form)
{
	global $sys, $db, $Bbc;
	$conf = get_config('guestbook', 'guestbook');
	$q = "UPDATE $form->table SET `date`=NOW(), publish=".@intval($conf['approved'])." WHERE id=$form->table_id";
	$db->Execute($q);
	$q = "SELECT * FROM $form->table WHERE id=$form->table_id";
	$data		= $db->getRow($q);
	$params	= array_merge($data, config_decode($data['params']));
	unset($params['id'], $params['date'], $params['publish'], $params['params']);
	if($conf['alert'])
	{
		$d = 'User Profile :';
		foreach($params AS $key => $value)
		{
			$d .= "\n".$key.' : '.$value;
		}
		$params['detail'] = $d;
		$email = is_email($conf['email']) ? $conf['email'] : config('email','address');
		$to = array($data['email'], $email);
		$sys->mail_send($to, 'guestbook', $params);

		$post    = array(
			'url_admin' => 'index.php?mod=guestbook.list_detail&id='.$form->table_id
			);
		_func('alert');
		alert_add(lang('Guest Book::').' '.$data['name'], $params['message'], $post, 'admin');
	}
	$message = $sys->text_replace(lang('finished'));
	$_SESSION['guestbook'] = $message;
	redirect($Bbc->mod['circuit'].'.form-finished');
}
