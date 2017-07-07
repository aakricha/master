<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$conf = get_config('testimonial', 'testimonial');
_func('avatar');
_func('smiley');
$sql = 'ORDER BY ';
switch(@$conf['orderby'])
{
	case '2': $sql .= '`id` ASC';break;
	case '3': $sql .= '`name` ASC';break;
	default	: $sql .= '`id` DESC';break;
}
$page = @intval($_GET['id']);
$sql .= ' LIMIT '.@intval($page*$conf['tot']).', '.@intval($conf['tot']);
$q = "SELECT SQL_CALC_FOUND_ROWS * FROM testimonial WHERE publish=1 ".$sql;
$r_list = $db->getAll($q);
if($db->Affected_rows())
{
	include tpl('list_show.html.php');
	if($Bbc->mod['task'] != 'list_show')	{
		$found= $db->getOne('SELECT FOUND_ROWS()');
		echo page_list($found, $conf['tot'], $page, 'id', $Bbc->mod['circuit'].'.list');
	}
}else{
	echo msg(lang('testimonial empty'));
}
if($Bbc->mod['task'] == 'list_show') $sys->stop();
