<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tabs = array();
$form = _class('bbcconfig');

$_setting = array(
	'alert'		=> array(
		'text'		=> 'Alert New Post'
	,	'type'		=> 'radio'
	,	'option'	=> array('1'=>'yes','0'=>'no')
	,	'default'	=> '1'
	,	'tips'		=> 'Alert admin by email for every new post'
	)
,	'email'		=> array(
		'text'		=> 'Email'
	,	'type'		=> 'text'
	,	'attr'		=> 'size="30"'
	,	'tips'		=> 'Insert email address as destination of new testi posted, or leave it blank to use global email'
	)
,	'approved'	=> array(
		'text'		=> 'Approved'
	,	'type'		=> 'radio'
	,	'option'	=> array('1'=>'auto','0'=>'manual')
	,	'default'	=> '0'
	,	'tips'		=> 'if auto, all incoming testi will automaticaly publish'
	)
,	'tot'=> array(
		'text'		=> 'Total per page'
	,	'type'		=> 'text'
	,	'attr'		=> 'size="10"'
	,	'default'	=> '12'
	,	'tips'		=> 'Items to show per page'
	)
,	'avatar'		=> array(
		'text'		=> 'Use Avatar'
	,	'type'		=> 'radio'
	,	'option'	=> array('1'=>'yes','0'=>'no')
	,	'default'	=> '1'
	,	'tips'		=> 'Show user avatar'
	)
,	'animated'		=> array(
		'text'		=> 'Load Page'
	,	'type'		=> 'radio'
	,	'option'	=> array('1'=>'Animated','0'=>'Manual')
	,	'default'	=> '0'
	,	'tips'		=> 'Select method to show testimonial list per page'
	)
,	'orderby'		=> array(
		'text'		=> 'Sequence from'
	,	'type'		=> 'radio'
	,	'option'	=> array('1'=>'Last Posted','2'=>'First Posted', '3'=>'Alphabetically')
	,	'default'	=> '1'
	)
);
$output = array(
	'config'=> $_setting
,	'name'	=> 'testimonial'
,	'title'	=> 'Testimonial List'
);
$form->set($output);
$tabs['List'] = $form->show();

$form = _lib('pea', 'testimonial_field');
$form->initRoll( "WHERE 1 ORDER BY orderby", 'id' );
$form->roll->setDeleteTool(false);

$form->roll->addInput( 'title', 'sqllinks' );
$form->roll->input->title->setTitle( 'Title' );
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.setting_field_edit' );

$form->roll->addInput( 'orderby', 'orderby' );
$form->roll->input->orderby->setTitle( 'Ordered' );

$form->roll->addInput( 'mandatory', 'checkbox' );
$form->roll->input->mandatory->setTitle( 'not null' );
$form->roll->input->mandatory->setCaption( 'yes' );

$form->roll->addInput( 'active', 'checkbox' );
$form->roll->input->active->setTitle( 'Active' );
$form->roll->input->active->setCaption( 'active' );

$tabs['Fields'] = $form->roll->getForm();
$tabs['Fields'] .= '<input type="button" class="button" style="float: right;" value="Advance Setting &gt;&gt;" onclick="document.location.href=\''.$Bbc->mod['circuit'].'.setting_field\'"><br class="clear" />';

echo tabs($tabs);