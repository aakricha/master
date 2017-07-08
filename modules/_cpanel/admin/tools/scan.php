<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Scan Error');
$output = '';
$form = _lib('pea', 'jcamp_log');

if($_GET['act'] == 'scan_files')
{
	_func('path');
	$folder_errors = array();

	tools_scan_check_dir_perm($folder_errors, _ROOT.'modules/', '755');
	tools_scan_check_dir_perm($folder_errors, _ROOT.'blocks/', '755');
	tools_scan_check_dir_perm($folder_errors, _ROOT.'templates/', '755');
	$r = $db->getCol("SELECT name FROM bbc_template");
	foreach((array)$r AS $d)
	{
		tools_scan_check_file_perm($folder_errors, _ROOT.'templates/'.$d.'/css/style.css', '777');
	}
	tools_scan_check_dir_perm($folder_errors, _ROOT.'images/', '777');
	$output .= implode('', $folder_errors);
	if(empty($output))
	{
		echo msg('All important folders have been checked and the permission access are okay!', 'Message : ');
	}else{
		echo $output;
	}
}
?>
<ul class="list-inline">
	<li><input type="button" value="&laquo; back" class="btn btn-default" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools'"></li>
	<li><input type="button" value="Scan Files &raquo;" class="btn btn-default" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools&act=scan_files'"></li>
	<li><input type="button" value="Get Command &raquo;" class="btn btn-default" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools&act=scan_command'"></li>
	<li><input type="button" value="Chmod Tool &raquo;" class="btn btn-default" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools&act=scan_chmod'"></li>
	<li><input type="button" value="Repair Database &raquo;" class="btn btn-default" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools&act=scan_database'"></li>
</ul>
<?php
function tools_scan_check_file_perm(&$folder_errors, $baseDir, $right_chmod)
{
	$permision = file_octal_permissions(fileperms($baseDir));
	if($permision != $right_chmod)
		$folder_errors[] = msg("Incorrect permision file ({$permision}), it should be {$right_chmod}", 'File : '.preg_replace('~^'.preg_quote(_ROOT, '~').'~is', '', $baseDir).'<br />');
}
function tools_scan_check_dir_perm(&$folder_errors, $baseDir, $right_chmod)
{
	$r = path_list_r($baseDir, true);
	foreach((array)$r AS $d)
	{
		if(is_dir($baseDir.$d))
		{
			$folder = $baseDir.$d.'/';
			$permision = file_octal_permissions(fileperms($folder));
			if($permision != $right_chmod)
			{
				$folder_errors[] = msg("Incorrect permision directory ({$permision}), it should be {$right_chmod}", 'Directory : '.preg_replace('~^'.preg_quote(_ROOT, '~').'~is', '', $folder).'<br />');
			}
			tools_scan_check_dir_perm($folder_errors, $folder, $right_chmod);
		}
	}
}