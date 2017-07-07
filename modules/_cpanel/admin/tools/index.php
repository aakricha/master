<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

icon('fa-scan')
?>
<ul class="list-inline">
	<li><a href="<?php echo $Bbc->mod['circuit'];?>.tools&act=scan" title="Scan Error" class="btn btn-default"><span class="fa fa-search-plus"></span> Scan Error</a></li>
	<li><a href="<?php echo $Bbc->mod['circuit'];?>.tools&act=module" title="Module Installer" class="btn btn-default"><span class="fa fa-cog"></span> Module Installer</a></li>
	<li><a href="<?php echo $Bbc->mod['circuit'];?>.tools&act=block" title="Block Installer" class="btn btn-default"><span class="fa fa-cogs"></span> Block Installer</a></li>
	<li><a href="<?php echo $Bbc->mod['circuit'];?>.tools&act=template" title="Template Installer" class="btn btn-default"><span class="fa fa-list-alt"></span> Template Installer</a></li>
	<li><a href="<?php echo $Bbc->mod['circuit'];?>.tools&act=language" title="Language Installer" class="btn btn-default"><span class="fa fa-language"></span> Language Installer</a></li>
	<li><a href="<?php echo $Bbc->mod['circuit'];?>.tools&act=licence" title="Logo Licence" class="btn btn-default"><span class="fa fa-picture-o"></span> Logo Licence</a></li>
</ul>