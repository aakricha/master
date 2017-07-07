<?php if (!defined('_VALID_BBC')) define('_VALID_BBC', '1');

require_once dirname(dirname(__FILE__)).'/config.php';
require_once _ROOT.'includes/lib/excel/excel.php';
include_once __DIR__.'/phpReport.php';

class phpRollExcel extends phpReport
{
	function __construct( $fileName='', $worksheetName='', $arrHeader=array(), $arrData = array() )
	{
		$tgl	= date('Y-m-d');

		if ( $fileName == '' )		$fileName = 'excelReport'. $tgl .'.xls';
		if ( $worksheetName == '' )	$worksheetName = 'Excel Report '. $tgl;

		$this->type          = 'excel';
		$this->fileName      = $fileName;
		$this->worksheetName = $worksheetName;
		$this->arrHeader     = $arrHeader;
		$this->arrData       = $arrData;
		// $this->setMaxColumnWidth();
		// $this->setHeaderColor();
	}

	function write()
	{
		$data = array(
			$this->worksheetName => array_merge(array($this->arrHeader), $this->arrData)
			);
		$excel = new excel();
		$excel->create($data)->download($this->fileName);
	}
}
