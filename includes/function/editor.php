<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*=============================================
 * sample $config = array(
 *									'toolbar'         => 'Basic' || 'Full'
 *								, 'DefaultLanguage' => 'en'
 *								, 'path'            => 'images/modules/content' -> path to upload images if empty it will go to "images/uploads"
 *								, 'width'           => '100%'
 *								, 'height'          => '120px'
 *								);
/*============================================*/
function editor_html($name, $value = '', $config = array(), $is_inline = false)
{
	global $sys;
	$sys->link_js(_URL.'includes/lib/ckeditor/ckeditor.js', false);
	ob_start();
	if(is_object($config))
	{
		$config = get_object_vars($config);
	}
	if(!empty($config['ToolbarSet']))
	{
		$config['toolbar'] = $config['ToolbarSet'];
		unset($config['ToolbarSet']);
	}
	if(!empty($config['Config']))
	{
		$config = array_merge($config, $config['Config']);
		unset($config['Config']);
	}
	$func   = $is_inline ? 'inline' : 'replace';
	$value2 = htmlentities($value, ENT_COMPAT, 'UTF-8', FALSE);
	if (!empty($value2))
	{
		$value = $value2;
	}
	$attr = '';
	if (!empty($config['attr']))
	{
		$attr = $config['attr'];
		unset($config['attr']);
	}
	$attr .= ' rel="ckeditor"';
	if (!empty($config['path']))
	{
		$path = str_replace(array(_URL, _ROOT), array('',''), $config['path']);
		if (!empty($path) && is_dir(_ROOT.$path))
		{
			$config['path'] = $path;
			$attr          .= ' data-path="'.$path.'"';
		}else unset($config['path']);
	}
	?>
	<textarea id="<?php echo $name;	?>" name="<?php echo $name;	?>"<?php echo $attr; ?>><?php echo $value;?></textarea>
	<script type="text/javascript"> CKEDITOR.<?php echo $func;?>('<?php echo $name;?>',<?php echo json_encode($config); ?>); </script>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
/*=============================================
 * show text editor to edit html..
 * sample $config = array(
	 		start_highlight: true
		,	allow_toggle: false
		,	language: "en"
		,	syntax: "html"	// (css|html|js|php|python|vb|xml|c|cpp|sql|basic|pas|brainfuck)
		,	toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
		,	syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck"
		,	is_multi_files: true
		,	fullscreen: false
		,	EA_load_callback: "editAreaLoaded"
		,	show_line_colors: true
 	 );
/*============================================*/
function editor_code($name, $value = '', $config = array(), $meta = true)
{
	global $sys;
	$output = '';
	$params = array(
		'id'                    => '',
		'start_highlight'       => true,
		'show_line_colors'      => true,
		'allow_toggle'          => false,
		'word_wrap'             => false,
		'replace_tab_by_spaces' => 2,
		'width'                 => '100%',
		'height'                => '350px',
		'syntax'                => 'html'
		);
	if(is_array($name)) $params = array_merge($params, $name);
	else $params['id'] = $name;
	_func('array');
	$sys->link_js(_URL.'includes/lib/ckeditor/filemanager/jscripts/edit_area/edit_area_full.js', $meta);
	$attr = '';
	if (!empty($config['attr']))
	{
		$attr = $config['attr'];
		unset($config['attr']);
	}
	$params = array_merge($params, (array)$config);
	if(is_array($value))
	{
		$params['is_multi_files'] = true;
		if(@empty($params['EA_load_callback']))
		{
			$sys->link_js(_URL.'includes/lib/ckeditor/filemanager/jscripts/edit_area/init_multifile.js', $meta);
			$params['EA_load_callback'] = 'init_multifile_load';
			$params['submit_callback'] = 'init_multifile_submit';
		}
		foreach($value AS $id => $data)
		{
			if(is_array($data)) {
				@list($title, $text) = $data;
			}else{
				$title= '';
				$text	= $data;
			}
			$name = $params['id'].'['.$id.']';
			$output .= '<textarea title="'.$title.'" name="'.$name.'" style="display: none;"'.$attr.'>'.htmlentities($text, ENT_COMPAT, 'UTF-8', FALSE).'</textarea>'."\n";
		}
		$name = $value = '';
	}else{
		$name = ' name="'.$params['id'].'"';
	}
	$output .= '<textarea id="'.$params['id'].'"'.$name.' style="width:'.$params['width'].';height:'.$params['height'].';"'.$attr.'>'.htmlentities($value, ENT_COMPAT, 'UTF-8', FALSE).'</textarea>'
	.	'<script type="text/javascript">if(typeof editAreaLoader!=\'undefined\'){editAreaLoader.init('.array_json($params).');}</script>';
	return $output;
}
