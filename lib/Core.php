<?php
$_CORE['CLI'] = isset($_SERVER['DOCUMENT_ROOT']) ? false : true;
if ((!isset($_CORE) || !$_CORE['CLI']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/.block")) {
	header("location: /block/");
	die;
}
$micro = microtime();
ini_set('display_errors', '0');
error_reporting(E_ALL);
date_default_timezone_set('Europe/Moscow');
require_once 'ERRH/errh.php';
require_once __DIR__ . '/common.php';
require_once 'ErrorCodes.php';
require_once 'Logger.php';
require_once 'ProtoArrayIterator.php';
require_once 'Processing.php';
require_once 'ProtoHTML2/ProtoHTML.php';
require_once 'QCache.php';
QCache::setCacheDir(__DIR__ . "/../cache");
require_once 'SQL.php';
require_once 'QDB2/QDB.php';
function __autoload ($class) {
	require_once __DIR__ . "/mod/$class.php";
	$class::init(Core::$db);
	return;
}
class Core {
	public static $cli;
	public static $self;
	public static $db;
	public static $doc;
	public static $docRoot;
	private $config;
	private $log;
	private $httpDocRoot;
	private $httpPath;
	public static $currentURL;
	public function __construct() {
		global $micro;
		global $_CORE;
		static::$cli = (@$_SERVER['DOCUMENT_ROOT']) ? false : true;
		if (static::$cli) {
			ERRH::$logFile = false;
			ERRH::$logHTML = false;
			ERRH::$logJS = false;
			ERRH::$logText = true;
		}
		$this->initPaths();
		$this->log = new Logger (__DIR__ . "/../logs", $micro);
		$this->log("Include '" . __DIR__ . "/config.php'");
		$this->config = include(__DIR__ . '/config.php');
		if (!$this->config['log']) {
			$this->log
				->mute();
		}
		$this->log("Connect DB");
		self::$db = new QDB($this->config['db']);
		$this->log("Create default DOM");
		if (!isset($_CORE['TEMPLATE'])) {
			$_CORE['TEMPLATE'] = __DIR__ . '/../templates/newmain.php';
		}
		if (!@$_CORE['DOC_DENY'] && !static::$cli) {
			$this->createDoc();
		}
		if (!isset($_SESSION['lang'])) {
			$_SESSION['lang']='ru';
		}
		$this->aliases();
		return;
	}
	public function __destruct() {}
	private function initPaths() {
		self::$docRoot = realpath (__DIR__ . '/../');
		$this->httpDocRoot = "/";
		$this->httpPath = substr(getcwd(), strlen(self::$docRoot));
		self::$currentURL = @$_SERVER['REQUEST_URI'];
		return;
	}
	private function createDoc() {
		global $_CORE;
		self::$doc = new ProtoHTMLDocument();
		self::$doc->css('/css/linker.css');
		self::$doc->css('/outsrc/jquery-ui-1.11.2/themes/smoothness/jquery-ui.min.css');
		self::$doc->css('/css/jquery-ui-fix.css');
		self::$doc->js('/outsrc/jquery/jquery-2.1.1.min.js');
		self::$doc->js('/outsrc/jquery-ui-1.11.2/jquery-ui.min.js');
		self::$doc->dir($this->config['dir']['js']);
		self::$doc->dir($_SERVER['DOCUMENT_ROOT'] . '/outsrc/contextMenu/');
		self::$doc->js('/outsrc/tinymce/jquery.tinymce.min.js');
		if ($_CORE['TEMPLATE']) {
			self::$doc->template($_CORE['TEMPLATE']);
		}
		global $Content;
		$Content = self::$doc->content;
		return;
	}
	public function log($msg) {
		return $this->log
					->log($msg);
	}
	public function __get($name) {
		switch ($name) {
			case 'db': 
				return $this->db; 
			break;
			case 'doc': 
				return $this->db; 
			break;
		}
		return null;
	}
	private function aliases() {
		self::$self = $this;
		class_alias('ProtoHTML', 'HTML');
	}
}

new Core();
?>
