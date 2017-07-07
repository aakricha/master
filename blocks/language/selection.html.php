<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$arr = array();
$lang = '';
foreach($r AS $lang_id => $dt)
{
	$arr[] = array($dt['code'], $dt['title']);
	if($lang_id == lang_id())
	{
		$lang = $dt['code'];
	}
}
?>
<select class="form-control" onchange="return ch_lang(this.value)"><?php echo createOption($arr, $lang);?></select>
<script type="text/javascript">
function ch_lang(a)
{
  var b = new RegExp(_URL, "g");
  var c = document.location.href.replace(b,'/');
  var b = new RegExp('/([a-z]{2})/');
  var d = b.exec(c);
  var e = _URL+a+'/';
  if(d == null) {
    b = new RegExp('/(.*?)$'); 
  }else{
    b = new RegExp('/[a-z]{2}/(.*?)$'); 
  }
  d = b.exec(c);
  if(d != null)
  {
    e += d[1];
  }
  document.location.href = e;
  return false;
};
</script>
