<?php
// библиотека для обработки внутренних ошибок
// может осуществлять вывод ошибок в файл, в консоль, в консоль браузера и непосредственно в окно браузера в формате HTML
ini_set('display_errors', '0');
error_reporting(E_ALL);
set_error_handler('ERRH::error');
register_shutdown_function('ERRH::fatalError');
class ERRH {
	// отвечает за добавление стилевого оформления при выводе ошибок в формате HTML
	private static $cssLinked = false;
	// отвечает за добавление JS-скрипта, отвечающего за удобный механизм сворачивания
	// и разворачивания подробного описания ошибок
	private static $jsLinked = false;
	// отображает или не отображает ошибки, возникающие в самой библиотеке,
	// используется для отладки библиотеки
	private static $debug = false;
	// вывод ошибок в консоль JS браузера
	public static $logJS = true;
	// вывод ошибок в формате HTML
	public static $logHTML = true;
	// вывод ошибок в файл
	// в случае, если задана Истина, выводит ошибки в файл, указанный в свойстве ERRH::logFileName
	// если ошибка вызвана внутри CLI-скрипта, то файл сгенерируется в текущей директории, где исполняется скрипт
	// в противном случае, файл будет создан в корне сайта
	// указывается информация о времени возникновения ошибки, пути к скрипту, вызвавшего ошибку, и краткое описание самой ошибки.
	public static $logFile = false;
	// вывод ошибок простым текстом (например в консоль, в случае, если инициатор - CLI-скрипт)
	public static $logText = false;
	// флаг, отвечающий за краткий или подробный (с полной трасировкой) вывод ошибок
	public static $brief = false;
	// имя файла, куда будет осуществляться запись ошибок (в случае указания свойства ERRH::logFile)
	public static $logFileName = 'err.log';
	public static function error($level, $msg, $file, $line, $context, $fatal = false) {
		if (!error_reporting()) {
			return;
		}
		if ($file == __FILE__ && !self::$debug) {
			echo 'ERROR HANDLER INTERNAL ERROR';
			die;
		}
		$err = array(
				'level' => $level,
				'name' => self::__getErrorName($level),
				'msg' => $msg,
				'file' => $file,
				'line' => $line,
				'context' => $context,
				);
		$pop = 0;
		if (!self::$debug) {
			if ($fatal) {
				$pop = 2;
			}
			else {
				$pop = 1;
			}
		}
		$backtrace = self::__getBacktrace ($err, $pop);
		if (self::$logHTML) {
			self::__printHTML($err, $backtrace);
		}
		if (self::$logJS) {
			self::__printJS($err, $backtrace);
		}
		if (self::$logFile) {
			self::__printFile($err, $backtrace);
		}
		if (self::$logText) {
			self::__printText($err, $backtrace);
		}
		self::__errorDie($level);
		return true;
	}
	public static function fatalError() {
		$file = "unknown file";
		$msg  = "shutdown";
		$level   = E_CORE_ERROR;
		$line = 0;
		$error = error_get_last ();
		if ($error === NULL) {
			return true;
		}
		$level   = $error["type"];
		$file = $error["file"];
		$line = $error["line"];
		$msg  = $error["message"];
		self::error ($level, $msg, $file, $line, NULL, true);
		return true;
	}
	private static function __incCSS() {
		if (self::$cssLinked) {
			return;
		}
		$f = fopen(__DIR__ . '/errh.css', 'r');
		$css = fread($f, filesize(__DIR__ . '/errh.css'));
		echo "<style>\n$css\n</style>\n";
		fclose($f);
		self::$cssLinked = true;
	}
	private static function __incJS() {
		echo "<script type = \"text/javascript\" src = \"/outsrc/jquery/jquery-2.1.1.min.js\"></script>";
		if (self::$jsLinked) {
			return;
		}
		$f = fopen(__DIR__ . '/errh.js', 'r');
		$js = fread($f, filesize(__DIR__ . '/errh.js'));
		echo "<script type = \"text/javascript\">\n$js\n</script>\n";
		fclose($f);
		self::$jsLinked = true;
	}
	private static function __getErrorName($level) {
		switch ($level) {
			case E_ERROR: 
				$res = 'Fatal run-time error'; 
			break;
			case E_WARNING: 
				$res = 'Run-time warning'; 
			break;
			case E_PARSE: 
				$res = 'Compile-time parse error'; 
			break;
			case E_NOTICE: 
				$res = 'Run-time notice'; 
			break;
			case E_CORE_ERROR: 
				$res = 'Fatal error'; 
			break;
			case E_CORE_WARNING: 
				$res = 'Warning'; 
			break;
			case E_COMPILE_ERROR: 
				$res = 'Fatal compile-time error'; 
			break;
			case E_COMPILE_WARNING: 
				$res = 'Compile-time warning'; 
			break;
			case E_USER_ERROR: 
				$res = 'User-generated error'; 
			break;
			case E_USER_WARNING: 
				$res = 'User-generated warning'; 
			break;
			case E_USER_NOTICE: 
				$res = 'User-generated notice'; 
			break;
			case E_STRICT: 
				$res = 'Strict'; 
			break;
			case E_RECOVERABLE_ERROR: 
				$res = 'Catchable fatal error'; 
			break;
			case E_DEPRECATED: 
				$res = 'Run-time depricated notice'; 
			break;
			case E_USER_DEPRECATED: 
				$res = 'User-generated warning depricated message'; 
			break;
			default: 
				$res = 'unknown error'; 
			break;
		}
		return $res;
	}
	private static function __errorDie($level) {
		if ($level == E_ERROR || $level == E_CORE_ERROR || $level == E_COMPILE_ERROR
		|| $level == E_USER_ERROR || $level == E_RECOVERABLE_ERROR || $level == E_PARSE) {
			die;
		}
	}
	private static function __getBacktrace($append, $pop = 0) {
		$backtrace = debug_backtrace();
		$backtrace = array_reverse($backtrace);
		array_pop($backtrace);
		for ($i = 0; $i < $pop; $i++) {
			array_pop($backtrace);
		}
		if ($append) {
			$backtrace [] = $append;
		}
		$res = array ();
		foreach ($backtrace as $row) {
			$row['file'] = (isset($row['file'])) ? $row['file'] : 'unknown file';
			$row['line'] = (isset($row['line'])) ? $row['line'] : 'unknown line';
			$row['class'] = (isset($row['class'])) ? $row['class'] : '';
			$row['object'] = (isset($row['object'])) ? get_class ($row['object']) : '';
			$row['type'] = (isset($row['type'])) ? $row['type'] : '';
			$row['function'] = (isset($row['function'])) ? $row['function'] : 'unknown_function';
			$row['args'] = (isset($row['args'])) ? $row['args'] : array();
			$row['format_function'] = self::__generateFunction($row);
			$res [] = $row;
		}
		return $res;
	}
	private static function __generateFunction($data) {
		$res = '';
		if ($data['type'] == '::' && $data['class'] == 'QDB' && $data['function'] == '__construct') {
			$args = ' ****** ';
		}
		else {
			$args = self::__generateArgs(@$data['args']);
		}
		switch ($data['type']) {
			case '::':
				$res = $data['class'] . $data['type'] . $data['function'] . "($args);";
			break;
			case '->':
				$res = $data['object'] . $data['type'] . $data['function'] . "($args);";
			break;
			default:
				$res = $data['function'] . "($args);";
			break;
		}
		return $res;
	}
	private static function __generateArgs($args) {
		$res = '';
		if (!$args) {
			return $res;
		}
		foreach ($args as $arg) {
			if ($res) {
				$res .= ', ';
			}
			$res .= self::__generateArg($arg);
		}
		return $res;
	}
	private static function __generateArg($arg) {
		$res = $arg;
		if ($arg === NULL) {
			$res = 'null';
		}
		if (is_bool($arg)) {
			$res = ($arg) ? 'true' : 'false';
		}
		if (is_string($arg)) {
			$res = "\"$arg\"";
		}
		if (is_array($arg)) {
			if (count($arg) == 0) {
				$res = "[]";
			}
			else {
				$res = "[...]";
			}
		}
		if (is_object($arg)) {
			$res = get_class($arg) . " object";
		}
		return $res;
	}
	private static function __printHTML($error, $backtrace) {
		self::__incJS();
		self::__incCSS();
		$color = self::__getColorByLevel($error['level']);
		echo "<div class = \"ERRH $color\">\n";
		$msg = $error['msg'];
		echo "\t<div title = '{$error['name']} (Code: {$error['level']})'>$msg</div>\n";
		if (!self::$brief) {
			echo "\t<ol>\n";
			foreach ($backtrace as $item) {
				$func = self::__highlight($item['format_function']);
				echo "\t\t<li>{$item['file']} ({$item['line']})<br>$func\n";
			}
			echo "\t</ol>\n";
		}
		echo "</div>\n";
		return;
	}
	private static function __printJS($error, $backtrace) {
		echo "<script>\n";
		echo "console.log('PHP ERRH {$error['name']} [{$error['level']}]:');\n";
		$msg = self::__jsAddSlashes($error['msg']);
		echo "console.log('$msg');\n";
		if (!self::$brief) {
			$pos = 0;
			foreach ($backtrace as $item) {
				$pos++;
				echo "console.log('$pos. {$item['file']} ({$item['line']})');\n";
				$func = self::__jsAddSlashes($item['format_function']);
				echo "console.log('$func');\n";
			}
			echo "console.log('');";
		}
		echo "</script>\n";
		return;
	}
	private static function __printFile($error, $backtrace) {
		$filename = self::$logFileName;
		if (isset($_SERVER) && isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
			$filename = $_SERVER['DOCUMENT_ROOT'] . "/" . $filename;
		}
		$bt = debug_backtrace();
		$top = array_pop($bt);
		$f = fopen($filename, 'a');
		$file = @$top['file'] ? $top['file'] : "Unknown file";
		fwrite($f, date("r") . "\n" . $file . "\n");
		fwrite($f, "{$error['name']}: {$error['msg']}\n");
		if (!self::$brief) {
			$pos = 0;
			foreach ($backtrace as $item) {
				$pos++;
				fwrite($f, "$pos. {$item['file']} ({$item['line']}): {$item['format_function']}\n");
			}
		}
		fwrite($f, "\n");
		fclose($f);
		return;
	}
	private static function __printText($error, $backtrace) {
		echo "{$error['name']}: {$error['msg']}\n";
		if (self::$brief) {
			return;
		}
		$pos = 0;
		foreach ($backtrace as $item) {
			$pos++;
			echo "$pos. {$item['file']} ({$item['line']}): {$item['format_function']}\n";
		}
		return;
	}
	private static function __jsAddSlashes($s) {
		$res = addslashes($s);
		$res = str_replace("\n", " ", $res);
		$res = str_replace("\r", " ", $res);
		return $res;
	}
	private static function __getColorByLevel($level) {
		$res = "grey";
		if ($level == E_ERROR || $level == E_CORE_ERROR || $level == E_COMPILE_ERROR
		|| $level == E_USER_ERROR || $level == E_RECOVERABLE_ERROR || $level == E_PARSE) {
			$res = 'red';
		}
		if ($level == E_WARNING || $level == E_CORE_WARNING || $level == E_COMPILE_WARNING
		|| $level == E_USER_WARNING || $level == E_STRICT || $level == E_DEPRECATED
		|| $level == E_USER_DEPRECATED) {
			$res = 'orange';
		}
		if ($level == E_NOTICE || $level == E_USER_NOTICE) {
			$res = 'green';
		}
		return $res;
	}
	private static function __highlight($s) {
		$res = highlight_string('<?php ' . $s, true);
		$res = str_replace('&lt;?php&nbsp;', '', $res);
		return $res;
	}
}
?>
