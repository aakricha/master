<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Scan Error Command');
$output = '';

_func('path');
_func('file');
/*================================================
 * GET CHMOD COMMAND
 *==============================================*/
if(!isset($_POST['Submit']))
{
?>
<form action="" method="POST" enctype="multipart/form-data" target="output">
	<table class="table">
		<tbody>
			<tr>
				<td width="50">user</td>
				<td>:<input type="text" name="user" class="form-control" /></td>
			</tr>
			<tr>
				<td>group</td>
				<td>:<input type="text" name="group" class="form-control" /></td>
			</tr>
			<tr>
				<td colspan=2>
					<input type="button" value="&laquo; back" class="btn btn-default" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools&act=scan'">
					<input type=submit name="Submit" class="btn btn-default" value="get command">
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<iframe src="" name="output" width="100%" height="300px" frameborder=0></iframe>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php
} else {
	$r_tpl= array();
	$text	= '';
	$path	= _ROOT.'templates/';
	$text .= "find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
";
	if(empty($_POST['user']) && preg_match('~^/home[0-9]?/~', $path))
	{
		preg_match('~/home[0-9]?/(.*?)/~', $path, $m);
		if(isset($m[1])) $_POST['user'] = $m[1];
	}

	if(isset($_POST['user']))
	{
		$user = $_POST['user'];
		$group= $_POST['group'] ? $_POST['group'] : $user;
		$text .= "chown -R $user:$group *
";
	}
	$r = path_list_r($path, true);
	foreach($r AS $dir)
	{
		if(!preg_match('/admin/i', $dir))
		{
			if(is_file($path.$dir.'/index.html'))
			{
				if(is_file($path.$dir.'/css/style.css'))
					$r_tpl[] = $path.$dir.'/css/style.css';
			}
		}
	}	
	$text .= "chmod -R 777 images/ .htaccess ".implode(' ', $r_tpl)."\n";
	echo '<textarea style="width:100%;height: 90%;border: 0px;background: transparent;" onclick="this.select();">'.$text.'</textarea>';
	die();
}

