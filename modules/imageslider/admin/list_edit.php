<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$q = "SELECT id, CONCAT(title,' (',width,'x',height,' pixel)') AS title FROM imageslider_cat ORDER BY title ASC";
$r_cat = $db->getAssoc($q);
$r_cat_key = array_keys($r_cat);

$form = _lib('pea', 'imageslider');
$form->initEdit('WHERE id='.@intval($_GET['id']));
$form->edit->setLanguage();

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Edit Image');

$form->edit->addInput('cat_id','select');
$form->edit->input->cat_id->setTitle('Category');
$form->edit->input->cat_id->addOptionArray($r_cat);

$form->edit->addInput('title','text');
$form->edit->input->title->setTitle('Title');
$form->edit->input->title->setSize(40);
$form->edit->input->title->setLanguage();

$form->edit->addInput('image','file');
$form->edit->input->image->setTitle('image');
$form->edit->input->image->setAllowedExtension(array('jpg', 'gif', 'png', 'bmp'));

$form->edit->addInput('link', 'text');
$form->edit->input->link->setTitle('Link');
$form->edit->input->link->setSize('40');

$form->edit->addInput('orderby', 'hidden');
$form->edit->input->orderby->setTitle('Orderby');
$form->edit->input->orderby->setDefaultValue(0);

$form->edit->addInput('publish', 'checkbox');
$form->edit->input->publish->setTitle('Publish');
$form->edit->input->publish->setCaption('Published');

$form->edit->onSave('imageslider_save');
$form->edit->action();
echo $form->edit->getForm();
