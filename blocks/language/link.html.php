<ul class="list-unstyled">
	<?php
	foreach((array) $r AS $lang_id => $dt)
	{
		$active = ($lang_id==lang_id()) ? ' class="active"' : '';
		?>
		<li class="text text-muted"><?php echo $active;?>><a href="#" rel="<?php echo $dt['code'];?>" onclick="return ch_lang(this.rel);"><?php echo $dt['title'];?></a></li>
		<?php
	}
	?>
</ul>