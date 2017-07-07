<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*==================================================
 * FORM ADD
 *================================================*/
$form1 = _lib('pea', 'survey_polling');
$form1->initAdd();
$form1->add->setLanguage( 'polling_id');

$form1->add->addInput('header','header');
$form1->add->input->header->setTitle('Add Polling');

$form1->add->addInput('question','textarea');
$form1->add->input->question->setTitle('Question');
$form1->add->input->question->setSize(4, 80);
$form1->add->input->question->setLanguage( true );

$form1->add->addInput('publish','checkbox');
$form1->add->input->publish->setTitle('Publish');
$form1->add->input->publish->setCaption('Actived');
$form1->add->input->publish->setDefaultValue(1);

$form1->add->onSave('_polling_add', $form1->add->getInsertId());
$form1->add->action();
function _polling_add($id)
{
	global $Bbc;
	if($id > 0)
	{
		redirect($Bbc->mod['circuit'].'.polling_edit&id='.$id);
	}
}

/*==================================================
 * FORM LIST
 *================================================*/
$form = _lib('pea', 'survey_polling');
$form->initSearch();

$form->search->addInput('keyword','keyword');
$form->search->input->keyword->addSearchField('question', true);

$add_sql = $form->search->action();
$keyword = $form->search->keyword();
echo $form->search->getForm();

$form = _lib('pea',  'survey_polling AS p LEFT JOIN survey_polling_text AS t ON (p.id=t.polling_id AND lang_id='.lang_id().')' );

$form->initRoll("$add_sql ORDER BY id", 'id' );
#$form->roll->setSaveTool(false);

$form->roll->addInput('question','sqllinks');
$form->roll->input->question->setTitle('Question');
$form->roll->input->question->setLinks( $Bbc->mod['circuit'].'.polling_edit');

$form->roll->addInput( 'id', 'selecttable' );
$form->roll->input->id->setTitle( 'options' );
$form->roll->input->id->setReferenceTable( 'survey_polling_option WHERE publish=1 GROUP BY polling_id' );
$form->roll->input->id->setReferenceField( 'COUNT(*)', 'polling_id' );
$form->roll->input->id->setPlaintext( true );

$form->roll->addInput('publish','checkbox');
$form->roll->input->publish->setTitle('Publish');
$form->roll->input->publish->setCaption('Actived');

$form->roll->onDelete('polling_delete', $form->roll->getDeletedId(), false);

function polling_delete($ids)
{
	global $db;
	if(count($ids) > 0)
	{
		$ids[] = 0;
		$q = "SELECT id FROM survey_polling_option WHERE polling_id IN(".implode(',', $ids).")";
		$option_ids = $db->getCol($q);
		if(count($option_ids) > 0)
		{
			$q = "DELETE FROM survey_polling_option_text WHERE polling_option_id IN(".implode(',', $option_ids).")";
			$db->Execute($q);
			$q = "DELETE FROM survey_polling_option WHERE id IN(".implode(',', $option_ids).")";
			$db->Execute($q);
		}
		$q = "DELETE FROM survey_polling WHERE id IN(".implode(',', $ids).")";
		$db->Execute($q);
		$q = "DELETE FROM survey_polling_text WHERE polling_id IN(".implode(',', $ids).")";
		$db->Execute($q);
	}
}
$tabs = array(
	'List Polling'=> $form->roll->getForm()
,	'Add Polling'	=> $form1->add->getForm()
);
echo tabs($tabs);
