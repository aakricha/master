<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*
UNTUK MEMBUAT MULTI SELECT DENGAN HUBUNGAN TABLE MANY TO MANY
$form->edit->addInput('NAMABEBAS','multiselect');
$form->edit->input->NAMABEBAS->setTitle('Reference Selection');
$form->edit->input->NAMABEBAS->setReferenceTable('bbc_content_cat_text');			# menentukan table reference
$form->edit->input->NAMABEBAS->setReferenceField('title','cat_id');						# menentukan field yang digunakan untuk table reference
#form->edit->input->NAMABEBAS->setReferenceCondition('lang_id='.lang_id());		# jika ada tambahan dalam pencarian di table reference (bisa banyak)
#form->edit->input->NAMABEBAS->setReferenceNested('par_id');									# jika reference table menggunakan par_id
#form->edit->input->NAMABEBAS->setRelationTable('bbc_content_category');			# jika menggunakan table sebagai relasi many to many (jika kosong maka akan menggunakan fieldName sebagai relasi dengan comma delimiter)
#form->edit->input->NAMABEBAS->setRelationTableId('category_id');							# jika table relasi menggunakan field primary yang auto_increment
#form->edit->input->NAMABEBAS->setRelationField('content_id','cat_id');				# menentukan field ang digunakan jika menggunakan table relasi (bukan lagi optional jika menggunakan table relasi)
#form->edit->input->NAMABEBAS->setRelationCondition('pruned=0');							# jika ada tambahan field dalam pencarian di table relasi (bisa banyak)
#form->edit->input->NAMABEBAS->setRelationCondition('active=1');							# jika ada tambahan field dalam pencarian di table relasi (bisa banyak)

UNTUK MEMBUAT MULTI SELECT TERSEBUT DALAM BENTUK DEPENDENTDROPDOWN
$form->edit->input->NAMABEBAS->setDependentDropdown();


terutama yang untuk hubungan many to many
jadi disini ada 3 tabel yang terlibat
	1. tabel main      = bbc_content
	2. tabel reference = bbc_content_cat_text
	3. tabel relation  = bbc_content_category (penghubung ke duanya)


contoh table:

	TABLE MAIN            TABLE RELATION     TABLE REFERENCE

 	id <----------------> content_id         title
 	title            			cat_id <---------> id
 	description
*/
include_once( _PEA_ROOT.'form/FormMulticheckbox.php' );

class FormMultiselect extends FormMulticheckbox
{
	var $isSingleSelect = false;
	var $referenceArray = array();
	var $defaultTip     = 'PS: Press and Hold the CMD / Ctrl button on your keyboard to select multiple options<br />';
	function __construct()
	{
		parent::__construct();
		$this->type 		= 'multiselect';
		$this->setSize(5);
		$this->addTip($this->defaultTip);
	}
	function setDependentDropdown($bool_is_multiple = true)
	{
		$this->isSingleSelect = $bool_is_multiple;
		if ($this->isSingleSelect)
		{
			$this->setReferenceNested(true);
			$this->textTip = str_replace($this->defaultTip, '', $this->textTip);
		}
	}
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		if ( $this->isPlaintext )
		{
			return $this->getPlaintexOutput( $str_value, $str_name, $str_extra );
		}
		$name		= ( $str_name == '' ) ? $this->name : $str_name;
		$extra	= $this->extra.' '. $str_extra;
		$output = array();
		$this->getDataFromReferenceTable();
		if ( !empty($this->referenceData['label']) )
		{
			$relationData	= $this->getDataFromRelationTable($str_value);
			foreach( $this->referenceData['label'] as $i => $label )
			{
				$checked= ( in_array( $this->referenceData['value'][$i], $relationData)  ) ? ' selected' : '';
				if($this->isPlaintext)
				{
					if($checked==' selected')
					{
						$output[]	= $label;
					}
				}else{
					$output[]	= '<option value="'.$this->referenceData['value'][$i].'"'.$checked.'>'.$label.'</option>';
				}
			}
		}
		if(!$this->isPlaintext)
		{
			if ($this->isSingleSelect)
			{
				link_js(_PEA_URL.'includes/FormMultiselect.js', false);
			}
			$input = implode('', $output);
			$cls   = $this->isSingleSelect ? 'form-inline FormMultiselect_single_select' : 'input-group';
			if ($this->isSingleSelect)
			{
				$json = '<div class="referenceArray hidden">'.json_encode($this->referenceArray).'</div>';
			}else{
				$json = '';
			}
			$out   = <<<EOT
<div class="{$cls}">
	<select name="{$name}[]" multiple size="{$this->size}"{$extra}>{$input}</select>
	<div class="input-group-addon">
		<input onclick="var v=$(this).parent().prev().get(0);for(i=0; i < v.options.length; i++)v.options[i].selected=this.checked;" type="checkbox">
	</div>
	{$json}
</div>
EOT;
		}else{
			$out = $this->getReturn(implode($this->delimiter, $output));
		}
		return $out;
	}
}
