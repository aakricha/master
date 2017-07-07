<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*========================================================
 *	$param = array(
 			'title'
 		,	'content'
 		,	'created'
 		,	'category'
 		,	'author'
 		,	'modified'
 		);
 *======================================================*/
function pdf_write($param, $paper = 'a4', $layout = 'portrait')
{
	$config = array('paper' => $paper, 'layout' => $layout);
	$pdf = _lib('pdf', $config);
	$pdf -> ezSetCmMargins( 2, 1.5, 1, 1);
	$pdf->selectFont( './fonts/Helvetica' ); //choose font

	$all = $pdf->openObject();
	$pdf->saveState();
	$pdf->setStrokeColor( 0, 0, 0, 1 );

	// footer
	$pdf->addText( 250, 822, 6, config('site','title') );
	$pdf->line( 10, 40, 578, 40 );
	$pdf->line( 10, 818, 578, 818 );
	$pdf->addText( 30, 34, 6, _URL);
	$pdf->addText( 250, 34, 6, 'Power By Fisip.net' );
	$pdf->addText( 450, 34, 6, 'Created '.$param['created'] );

	$pdf->restoreState();
	$pdf->closeObject();
	$pdf->addObject( $all, 'all' );
	$pdf->ezSetDy( 30, 'makeSpace' );

	$pdf->ezText( $param['title'], 16 );
	$pdf->ezText( pdf_date($param), 6 );
	if(!empty($param['image']))
	{
		$pdf->ezImage($param['image'],5,0,'full','left');
	}
	$pdf->ezText( pdf_cleaner($param['content']), 10 );

	$options = array(
		'Content-Disposition' => menu_save($param['title']).'.pdf'
	,	'Accept-Ranges'				=> 0
	);
	$pdf->ezStream($options);
	exit;
}

function pdf_decode( $string )
{
	$string = strtr( $string, array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
	$string = preg_replace_callback(
        '/&#([0-9]+);/m',
        function ($matches) {
            return chr($matches[1]);
        },
        $string
    );
	
	return $string;
}

function get_php_setting ($val )
{
	$r = ( ini_get( $val ) == '1' ? 1 : 0 );
	return $r ? 'ON' : 'OFF';
}

function pdf_cleaner( $text )
{	
	// Ugly but needed to get rid of all the stuff the PDF class cant handle
	$text = preg_replace("#\s{2,}#s", " ", $text );
	$text = str_replace( '<p>', 			"\n\n", 	$text );
	$text = str_replace( '<P>', 			"\n\n", 	$text );
	$text = str_replace( '<br />', 			"\n", 		$text );
	$text = str_replace( '<br>', 			"\n", 		$text );
	$text = str_replace( '<BR />', 			"\n", 		$text );
	$text = str_replace( '<BR>', 			"\n", 		$text );
	$text = str_replace( '<li>', 			"\n - ", 	$text );
	$text = str_replace( '<LI>', 			"\n - ", 	$text );
#	$text = str_replace( '{mosimage}', 		'', 		$text );
#	$text = str_replace( '{mospagebreak}', 	'',			$text );
	$text = strip_tags( $text, '<u>' );
	$text = pdf_decode( $text );
	return $text;
}
function pdf_date( $param )
{
	$text = '';
	$text .= $param['category'] ? "\n". $param['category'] : '';
	$text .= $param['author'] ? "\n". $param['author'] : '';
	$text .= $param['modified'] ? "\n". $param['modified'] : '';
	$text .= "\n\n";
	return $text;
}
?>