<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$form = _lib('pea', 'imageslider');
$form->initSearch();

$q = "SELECT id, CONCAT(title,' (',width,'x',height,' pixel)') AS title FROM imageslider_cat ORDER BY title ASC";
$r_cat = $db->getAssoc($q);
$r_cat_key = array_keys($r_cat);
if(count($r_cat_key) > 1)
{
	$form->search->addInput('cat_id','select');
	$form->search->input->cat_id->addOption('Select Category', '');
	$form->search->input->cat_id->addOption($r_cat, $r_cat_key);
}
$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('title', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$tabs = array('Images' => '', 'Add Image' => '');
$form = _lib('pea', 'imageslider');
$form->initEdit();
$form->edit->setLanguage();

$form->edit->addInput('header', 'header');
$form->edit->input->header->setTitle('Add New Image');

$form->edit->addInput('cat_id','select');
$form->edit->input->cat_id->setTitle('Category');
$form->edit->input->cat_id->addOptionArray($r_cat);
$form->edit->input->cat_id->setDefaultValue(@$keyword['cat_id']);

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
$form->edit->input->publish->setDefaultValue(1);

$form->edit->onSave('imageslider_save');
$tabs['Add Image'] = $form->edit->getForm();

/*==========================================
 * START LIST
/*=========================================*/
$form = _lib('pea', 'imageslider AS i LEFT JOIN imageslider_text AS t ON (i.id=t.imageslider_id AND t.lang_id='.lang_id().')');
$form->initRoll($add_sql.' ORDER BY orderby ASC', 'id');
#$form->roll->setLanguage();

$form->roll->addInput('title','sqllinks');
$form->roll->input->title->setTitle('Title');
$form->roll->input->title->setLinks( $Bbc->mod['circuit'].'.list_edit');
#$form->roll->input->title->setLanguage();

$form->roll->addInput('image','file');
$form->roll->input->image->setTitle('image');
$form->roll->input->image->setFolder($Bbc->mod['dir']);
$form->roll->input->image->setPlaintext( true );
$form->roll->input->image->setImageClick( true );

$form->roll->addInput('link', 'sqlplaintext');
$form->roll->input->link->setTitle('Link');

if(empty($keyword['cat_id']))
{
	$q = "SELECT DISTINCT cat_id FROM imageslider WHERE 1";
	$r = $db->getOne($q);
	$ord = (count($r) > 1) ? false : true;
}else $ord = true;
if($ord)
{
	$form->roll->addInput('orderby', 'orderby');
	$form->roll->input->orderby->setTitle('Orderby');
}else{
	$form->roll->addInput('cat_id','select');
	$form->roll->input->cat_id->setTitle('Category');
	$form->roll->input->cat_id->addOptionArray($r_cat);
	$form->roll->input->cat_id->setPlaintext( true );
}
$form->roll->addInput('publish', 'checkbox');
$form->roll->input->publish->setTitle('Publish');

$form->roll->onDelete('imageslider_delete');
$form->roll->onSave('imageslider_repair');

$tabs['Images'] = $form->roll->getForm();

echo tabs($tabs, 1, 'tabs_links');

