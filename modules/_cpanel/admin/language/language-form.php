<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

echo $form1->roll->getForm().$language_update;
?>
<input type="button" onclick="return super_update()" class="button" value="repair language" name="back"/>

<script type="text/javascript">
	function super_update()
	{
		if (confirm("Are you sure want to perform this action. it can caused any damage to your database ?"))
		{
			document.location.href='index.php?mod=_cpanel.language&act=super-update';
			return true;
		} else {
			return false;
		}
	}
</script>