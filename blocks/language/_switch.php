<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Apabila anda menggunakan multi language (Control Panel / Language / Language Reference) anda bisa menampilkan pilihan bahasa dengan block ini
$output=array();
$r = lang_assoc();
include tpl(@$config['template'].'.html.php', 'link.html.php');