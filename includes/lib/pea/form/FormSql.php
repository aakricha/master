<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// untuk membuat kolom yang berkondisi
// hanya bisa digunakan untuk roll

// addCondition( $operator, $comparator, $output = '' )
// 		untuk menambah kondisi
//		$operator	isinya {  '=', '==', '!=', '<>', '>', '<', '>=', '<=', 'default'  );
//		c: $this->addCondition( "=", 'makan', '<a href=index.php?makan>makan</a>' );
//		saat operatornya default, maka jika tidak ada kondisi yang emmenuhi, otomatis, outputnya adalah $output di default tersebut
//		c: $this->addCondition( 'default', '', '<a href=index.php?default>ini default</a>' );
//
//		$output bisa diisi dengan #_id_# yang nantinya akan di outputkan sebagai id dari row bersangkutan
//		c: $this->addCondition( '<>', 'makan', '<a href=index.php?id="#_id_#">ini default</a>' );
class FormSql extends Form
{
	var $operator;
	var $comparator;
	var $output;
	var $defaultCondition;

	function __construct()
	{
		$this->type = 'swl';
		$this->setIsIncludedInSearch( false );
		$this->setIsNeedDbObject( true );
		$this->setIsIncludedInUpdateQuery( false );
	}

	function setDelimiter( $str_delimiter	= '&nbsp;' )
	{
		$this->delimiter	= $str_delimiter;
	}

	function addSqlQuery( $sql )
	{
		$this->sql=$sql;

	}
	/*
	mendapatakan field yang di gunakan untuk kondisi sql,
	jika di temukan nanti di cek dulu di table utama ,apakah ada tidak field tersebut
	jika tidak ada langsung di die
	*/

	function parsingSql()
	{
		if ( preg_match("/{(.*?)}/",$this->sql,$match) )
		{
			/*
			echo "<pre>";
			print_r($match);
			echo "</pre>";
			die();
			*/
			$this->valueToReplace=$match[0]; // {blabla}
			$this->fieldToSelect=$match[1]; // nama field yang akan di cek

		}else
		{
			die("SQL query yg anda masukan harus mengandung {}");
			return ;
		}


	}

	// fungsi untuk mengecek apakah field tersebut ada di table utama atau tidak,
	// memanfaatkan nilai id yang ada di table utama,
	// sekalian mereturn kan nilainya yang di gunakan untuk quer
	function getFieldValue($value_id)
	{

		$q="SELECT ".$this->fieldToSelect." FROM `".$this->tableName."` WHERE ".$this->tableId."='".$value_id."'";
		$r=$this->db->Execute($q);

		if($r->RecordCount() < 1)
		{
			die("<strong>Nama Field yang ada di dalam tanda {} tidak ada di table ".$this->tableName.",<br>
				sql : ".$this->sql."</strong>");
		}else
		{
			$field_value=$this->db->GetOne($q);
			return $field_value;

		}

	}

	function getDataQuery($value)
	{


		$this->parsingSql();
		$field_value=$this->getFieldValue($value);



		$sql	= preg_replace( '~'.$this->valueToReplace.'~is', $field_value, $this->sql);
		$this->db->SetFetchMode(ADODB_FETCH_NUM);
		$r=$this->db->Execute($sql);
		$data=array();

		$data=$r->GetArray();

		return $data;

	}
	function getReportOutput( $str_value = '' )
	{

		$out	= $this->getOutput($str_value);

		return $out;
	}

	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{

		$data=$this->getDataQuery($str_value);
		$out ="";
		$extra	= $this->extra .' '. $str_extra;
		if(count($data) > 0)
		{

			foreach ($data as $i=>$arrValue)
			{

				foreach ($arrValue as $y=>$value)
				{
					if(!empty($value))
					$out .=$value ."".$this->delimiter."";

				}
			}

		}

		if ( $this->isPlaintext ) return $this->getPlaintexOutput( $out, $str_name, $str_extra );
		return $out;
	}
}