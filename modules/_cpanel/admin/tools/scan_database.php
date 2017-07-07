<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Repair Database');
$output = '';

/*================================================
 * GET CHMOD COMMAND
 *==============================================*/
if(!isset($_POST['Submit']))
{
	$f = _lib('pea', 'bbc_log');
?>
<form action="" method="POST" enctype="multipart/form-data" target="output">
	<table width="100%" height="100%">
		<tr>
			<td><?php echo explain('This action will perform to repair all tables in your database. This may take times to execute, and your server could be slow for a while.', 'Message :');?></td>
		</tr>
		<tr>
			<td>
				<input type="button" value="&laquo; Cancel" class="button" onClick="document.location.href='<?php echo $Bbc->mod['circuit'];?>.tools&act=scan'">
				<input type=submit name="Submit" class="button" value="Commit the action">
			</td>
		</tr>
		<tr>
			<td>
				<iframe src="" name="output" width="100%" height="300px" frameborder=0></iframe>
			</td>
		</tr>
	</table>
</form>
<?php
} else {
	$q = "SHOW TABLES";
	$r = $db->getCol($q);
	$q = "REPAIR TABLE `".implode('`, `', $r)."`";
	$db->Execute($q);
	echo '<textarea style="width:100%;height: 98%;border: 0px;">'.$q.'</textarea>';
	die();
}

