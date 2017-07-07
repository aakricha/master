<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class crypt
{
	private $use_sha = false;
	function __construct() {}
	public function encode($text)
	{
		$text = $this->sha5($text, true, $this->use_sha);
		$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$pad  = $size - (strlen($text) % $size);
		$text = $text.str_repeat(chr($pad), $pad);
		$td   = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
		$iv   = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, _SALT, $iv);
		$data = mcrypt_generic($td, $text);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$data = base64_encode($data);
		return $data;
	}
	public function decode($text)
	{
		$decrypted = @mcrypt_decrypt(
			MCRYPT_RIJNDAEL_128,
			_SALT,
			base64_decode($text),
			MCRYPT_MODE_ECB
			);
		$dec_s     = strlen($decrypted);
		$padding   = ord($decrypted[$dec_s-1]);
		$decrypted = substr($decrypted, 0, -$padding);
		$decrypted = $this->sha5($decrypted, false, $this->use_sha);
		return $decrypted;
	}
	public function sha5($string, $toogle, $use_sha = true)
	{
		if ($use_sha)
		{
			$o = '';
			$r = str_split($string);
			if ($toogle)
			{
				foreach ($r as $i => $a)
				{
					$j = rand(97, 122);
					if (rand(0,1)) {
						$j -= 32;
					}
					$x = ord($a)+$j;
					if ($x > 256)
					{
						$x -= 256;
					}
					$o .= chr($j).chr($x);
				}
			}else{
				$j = 0;
				foreach ($r as $i => $a)
				{
					if($i%2)
					{
						$x = ord($a)-$j;
						if ($x < 0)
						{
							$x += 256;
						}
						$o .= chr($x);
					}else{
						$j = ord($a);
					}
				}
			}
		}else{
			$o = $string;
		}
		return $o;
	}
}