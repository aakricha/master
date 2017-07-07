<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><?php echo $sys->meta();?></head>
<body style="background: #fff;" onload="document.getElementById('login-loading').style.display='none';document.getElementById('login-form').style.display='block';document.login_form.usr.focus();">
	<?php echo trim($Bbc->content);?>
</body>
</html>
