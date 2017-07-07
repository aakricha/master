<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$thisFile = _ROOT.'templates/'.$_CONFIG['template'].'/css/style.css';
if(@$_POST['edit_css']!='')
{
	@chmod($thisFile, 0777);
	$style = stripslashes($_POST['edit_css']);
	$style = preg_replace('~[\r\n]{2,}~is', "\n", $style);
	$style = preg_replace('~<BR.*?>~is', '', $style);
	if(file_write($thisFile, $style))
		$msg = 'Succeed to edit CSS.';
	else $msg = 'Failed to edit CSS, please try again later..!!';
	?>
	<script type="text/javascript">alert('<?php echo $msg;?>');</script>
	<?php
	die();
}

_func('editor');
$text_css = file_read($thisFile);
$config = array(
	'id'            => 'edit_css',
	'allow_resize'  => true,
	'word_wrap'     => false,
	'fullscreen'    => true,
	'syntax'        => 'css',
	'begin_toolbar' => 'save',
	'save_callback' => 'edit_submit',
	'height'        => '350px'
	);
?>
<script type="text/javascript">
	function edit_submit() {
		document.getElementById('edit_css').value = editAreaLoader.getValue('edit_css');
		document.forms['edit'].submit();
	}
</script>
<IFRAME name="save_css" src="" frameBorder="0" style="display: none;"></IFRAME>
<form method=post action="" id="edit" name="edit" target="save_css">
	<?php echo editor_code($config, $text_css);?>
</form>
