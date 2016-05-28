<?php
use Illuminate\Support\Facades\Input;

class Uchelper {
    protected static $_instance;
	public $_SGLOBAL, $_SC, $_SCONFIG, $_SBLOCK, $_TPL, $_SCOOKIE, $_SN, $space;
	public function __construct() {
		
		self::$_instance = $this;
		
		@define ( 'IN_UCHOME', TRUE );
		define ( 'D_BUG', '0' );
		define ( 'S_ROOT', public_path () . DIRECTORY_SEPARATOR );
		
		$this->_SC = include_once (S_ROOT . './config.php');
		
		// 时间
		$mtime = explode ( ' ', microtime () );
		$this->_SGLOBAL ['timestamp'] = $mtime [1];
		$this->_SGLOBAL ['supe_starttime'] = $this->_SGLOBAL ['timestamp'] + $mtime [0];
		
		// 本站URL
		if (empty ( $this->_SC ['siteurl'] ))
			$this->_SC ['siteurl'] = url ();
			
			// 链接数据库
		$this->dbconnect ();
		
		// 初始化
		$this->_SGLOBAL ['supe_uid'] = 0;
		$this->_SGLOBAL ['supe_username'] = '';
		$this->_SGLOBAL ['inajax'] = empty ( Input::get('inajax') ) ? 0 : intval ( Input::get('inajax') );
		$this->_SGLOBAL ['mobile'] = empty ( Input::get('mobile') ) ? '' : trim ( Input::get('mobile') );
		$this->_SGLOBAL ['ajaxmenuid'] = empty ( Input::get('ajaxmenuid') ) ? '' : Input::get('ajaxmenuid');
		$this->_SGLOBAL ['refer'] = empty ( $_SERVER ['HTTP_REFERER'] ) ? '' : $_SERVER ['HTTP_REFERER'];
		// if(empty(Input::get('m_timestamp')) || $this->_SGLOBAL['mobile'] != md5(Input::get('m_timestamp')."\t".$this->_SCONFIG['sitekey'])) $this->_SGLOBAL['mobile'] = '';
		
		$this->_SGLOBAL ['mobile'] = '1';
		
		// Input::get('m_auth') = rawurldecode ( Input::get('m_auth') );
		
		// 判断用户登录状态
		$this->checkauth ();
	}

	public static function getInstance()
	{
		return self::$_instance;
	}
	
	// 字符串解密加密
	public function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4; // 随机密钥长度 取值 0-32;
		                  // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
		                  // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
		                  // 当此值为 0 时，则不产生随机密钥
		
		$key = md5 ( $key ? $key : UC_KEY );
		$keya = md5 ( substr ( $key, 0, 16 ) );
		$keyb = md5 ( substr ( $key, 16, 16 ) );
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';
		
		$cryptkey = $keya . md5 ( $keya . $keyc );
		$key_length = strlen ( $cryptkey );
		
		$string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
		$string_length = strlen ( $string );
		
		$result = '';
		$box = range ( 0, 255 );
		
		$rndkey = array ();
		for($i = 0; $i <= 255; $i ++) {
			$rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
		}
		
		for($j = $i = 0; $i < 256; $i ++) {
			$j = ($j + $box [$i] + $rndkey [$i]) % 256;
			$tmp = $box [$i];
			$box [$i] = $box [$j];
			$box [$j] = $tmp;
		}
		
		for($a = $j = $i = 0; $i < $string_length; $i ++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box [$a]) % 256;
			$tmp = $box [$a];
			$box [$a] = $box [$j];
			$box [$j] = $tmp;
			$result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
		}
		
		if ($operation == 'DECODE') {
			if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {
				return substr ( $result, 26 );
			} else {
				return '';
			}
		} else {
			return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
		}
	}
	public function dbconnect() {
		include_once (S_ROOT . './source/class_mysql.php');
		
		if (empty ( $this->_SGLOBAL ['db'] )) {
			$this->_SGLOBAL ['db'] = new dbstuff ();
			$this->_SGLOBAL ['db']->charset = $this->_SC ['dbcharset'];
			$this->_SGLOBAL ['db']->connect ( $this->_SC ['dbhost'], $this->_SC ['dbuser'], $this->_SC ['dbpw'], $this->_SC ['dbname'], $this->_SC ['pconnect'] );
		}
	}
	
	// 判断当前用户登录状态
	public function checkauth() {
		$this->_SCOOKIE ['auth'] = Input::get('m_auth');
		if ($this->_SCOOKIE ['auth']) {
			@list ( $password, $uid ) = explode ( "\t", $this->authcode ( $this->_SCOOKIE ['auth'], 'DECODE' ) );
			$this->_SGLOBAL ['supe_uid'] = intval ( $uid );
			if ($password && $this->_SGLOBAL ['supe_uid']) {
				$query = $this->_SGLOBAL ['db']->query ( "SELECT * FROM " . $this->tname ( 'session' ) . " WHERE uid='" . $this->_SGLOBAL ['supe_uid'] . "'" );
				if ($member = $this->_SGLOBAL ['db']->fetch_array ( $query )) {
					if ($member ['password'] == $password) {
						$this->_SGLOBAL ['supe_username'] = addslashes ( $member ['username'] );
						$this->_SGLOBAL ['session'] = $member;
					} else {
						$this->_SGLOBAL ['supe_uid'] = 0;
					}
				}
			}
		}
	}
	
	// 获取到表名
	public function tname($name) {
		return $this->_SC ['tablepre'] . $name;
	}
}
?>
