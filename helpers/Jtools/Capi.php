<?php
namespace Jtools;
class Capi {
	static function mkjson($response = '', $callback = '') {
		if ($callback) {
			header ( 'Cache-Control: no-cache, must-revalidate' );
			header ( 'Content-Type: text/javascript;charset=utf-8' );
			echo $callback . '(' . json_encode ( $response ) . ');';
		} else {
			// application/x-json will make error in iphone, so I use the text/json
			// instead of the orign mine type
			header ( 'Cache-Control: no-cache, must-revalidate' );
			header ( 'Content-Type: text/json;' );
			
			echo json_encode ( $response );
		}
		exit ();
	}
	public static function showmessage_by_data($msgkey, $code = 1, $data = array()) {
		ob_clean ();
		
		// 语言
		$msglang = include __DIR__ . DIRECTORY_SEPARATOR . 'lang_showmessage.php';
		if (isset ( $msglang [$msgkey] )) {
			$message =  $msglang [$msgkey];
		} else {
			$message = $msgkey;
		}
		$r = array ();
		$r ['code'] = $code;
		$r ['data'] = $data;
		$r ['msg'] = $message;
		$r ['action'] = $msgkey;
		$callback = empty ( $_REQUEST ['callback'] ) ? '' : $_REQUEST ['callback'];
		self::mkjson ( $r, $callback );
	}
	
	// 语言替换
	static function lang_replace($text, $vars) {
		if ($vars) {
			foreach ( $vars as $k => $v ) {
				$rk = $k + 1;
				$text = str_replace ( '\\' . $rk, $v, $text );
			}
		}
		return $text;
	}
}
?>