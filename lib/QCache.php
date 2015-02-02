<?php
class QCache {
	private static $dir = '.';
	public static $enabled = true;
	public static function setCacheDir($dir) {
		static::$dir = $dir;
		if (is_dir(static::$dir)) {
			return;
		}
		mkdir(static::$dir);
		chmod(static::$dir, 0777);
		return;
	}
	public static function cache($key, $data = null, $period = null) {
		if (!self::$enabled) {
			return;
		}
		$filename = self::$dir . "/" . md5(json_encode($key)) . ".cache.php";
		if ($data === null) {
			return self::get($filename);
		}
		return self::set($filename, $data, $period);
	}
	private static function get($filename) {
		if (!is_file($filename)) {
			return;
		}
		$res = include $filename;
		if (!$res) {
			return;
		}
		if (!is_array($res)) {
			return;
		}
		if ($res['exp'] && $res['exp'] < time()) {
			self::del($filename);
			return;
		}
		return $res['data'];
	}
	private static function set($filename, $data, $period) {
		$f = @fopen($filename, 'w');
		if (!$f) {
			trigger_error("QCache: failed to open file '$filename'", E_USER_WARNING);
			return;
		}
		$exp = ($period === null) ? 0 : time() + $period;
		$data = array(
				'time' => time(),
				'exp' => $exp,
				'data' => $data,
		);
		fwrite($f, "<?php return " . var_export($data, true) . ";");
		fclose($f);
		return;
	}
	private static function del($filename) {
		unlink($filename);
		return;
	}
	public static function clear($period = null) {
		if ($period === null) {
			$dir = dir(self::$dir);
			while (false !== ($fn = $dir->read())) {
				if ($fn != '.' && $fn != '..' && $fn != '.gitignore') {
					unlink(self::$dir . '/' . $fn);
				}
			}
			return;
		}
	}
}
class QDCache {
	private $data = array();
	public function cache($key, $data = false) {
		if ($data === false) {
			return @$this->data[$key];
		}
		$this->data[$key] = $data;
		return;
	}
	public function key() {
		$argv = func_get_args();
		return md5(serialize($argv));
	}
	public function clear() {
		$this->data = array();
		return;
	}
}
?>