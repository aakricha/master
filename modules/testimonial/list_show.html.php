<ul class="list-unstyled"> 	
<?php foreach((array)$r_list AS $data) { ?>
	<li>
		<div class="blockquote">
			<div class="col-md-2 no-left">
				<?php if($conf['avatar']) echo avatar($data['email'], 70);	?>
			</div>
			<div class="col-md-10 no-right">
				<b><?php echo $data['name'];?></b>					
				<span><?php echo date('d M Y | H:i:s', strtotime($data['date']));?></span>
				<p><?php echo smiley_parse($data['message']);?></p>
			</div>
		</div>
		<div class="clearfix"></div>
		<br>
	</li>
<?php } ?>
	<div class="clearfix"></div>
</ul>
