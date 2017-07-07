<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$code		= $_GET['id'];
$r_lang = get_lang();
$lang_id = array_search($code, $r_lang);
if($lang_id > 0) {
	$_SESSION['lang_id'] = $lang_id;
	redirect();
}
