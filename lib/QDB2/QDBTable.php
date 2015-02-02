<?php
require_once 'QDBObject.php';
require_once 'QDBList.php';
require_once 'QDBConfig.php';
require_once 'QDBGenerator.php';
// класс организует работу с конкретной таблицей БД, для каждой конкретной таблицы этот класс должен быть переопределен
class QDBTable extends QDBObject {
	protected static $name;
	protected static $db;
	protected static $config;
	protected static $params;
	protected static $cache;
	public static $htmlcode = true; 
	public static $striptags = "<p><i><em><u><b><strong><h1><h2><h3><h4><h5><h6><a>"; 
	public static $noStripFields = [];
	public static function init (QDB $db) {
		static::$db = &$db;
		$tmpParams = array();
		static::$params = &$tmpParams; 
		$tmpConfig = new QDBConfig(get_called_class()); 
		static::$config = &$tmpConfig; 
		$tmpCache = new QDCache(); 
		static::$cache = &$tmpCache; 
		static::setParam(QDB::CACHE, QDB::ALLOWED);
		static::setParam(QDB::PARSER, QDB::ALLOWED);
		static::setParam(QDB::SELECT, QDB::ALIVE);
		static::setParam(QDB::DELETE, QDB::ARCHIVE);
		static::setParam(QDB::LIMIT, 0);
		return;
	}
	public static function setParam($name, $value = true) {
		static::$params[$name] = $value;
		return;
	}
	public static function getParam($name) {
		return static::$params[$name];
	}
	public static function getTableName() {
		return static::$name;
	}
	public static function getDB() {
		return static::$db;
	}
	public static function getConfig() {
		return static::$config;
	}
	// запрос к БД
	public static function query($sql, array $data = array(), $debug = false) {
		if (static::getParam(QDB::PARSER) == QDB::ALLOWED) {
			$sql = static::$config->parseSQL($sql);
			$limit = static::getParam(QDB::LIMIT);
			if ($limit) {
				$sql->limit = $limit;
			}
		}
		return static::$db->exec("$sql", $data, $debug);
	}
	/**
	 * @return QDBList
	 */
	// выборка нескольких объектов (записей)
	public static function select() {
		$argc = func_num_args();
		$argv = func_get_args();
		if ($argc == 1 && is_array($argv[0]) && !is_object($argv[0])) {
			$argv = $argv[0];
			$argc = count($argv);
		}
		$sql = ($argc > 0) ? $argv[0] : null;
		$data = ($argc == 2) ? $argv[1] : array();
		if (!is_array($data)) {
			trigger_error("QDBTable::select: second argument should be an array", E_USER_ERROR);
		}
		if (static::getParam(QDB::CACHE) == QDB::ALLOWED) {
			$key = static::$cache->key('select', $argv);
			if (!static::getParam(QDB::LIMIT) && $res = static::$cache->cache($key)) {
				return $res;
			}
		}
		$table = static::$name;
		$tmpsql = new SQL($sql);
		$fields = static::$config->genAllFields();
		if (!$tmpsql->select) {
			$sql = "select $fields from `$table` $sql";
		}
		$q = static::query($sql, $data);
		$res = new QDBList();
		while ($row = $q->fetch(QDB::FETCH_ASSOC)) {
			$res[] = new static($row);
		}
		if (static::getParam(QDB::CACHE) == QDB::ALLOWED && !static::getParam(QDB::LIMIT)) {
			static::$cache->cache($key, $res);
		}
		return $res;
	}
	/**
	 * @return QDBTable
	 */
	// получить объект (запись)
	public static function object() {
		$argc = func_num_args();
		$argv = func_get_args();
		if ($argc == 1 && is_array($argv[0]) && !is_object($argv[0])) {
			$argv = $argv[0];
			$argc = count($argv);
		}
		if ($argc == 1 && !$argv[0]) {
			return null;
		}
		if ($argc == 1 && is_numeric($argv[0])) {
			$pri = static::$config->pri;
			$argv[1] = array($argv[0]);
			$argv[0] = "where `$pri` = ?";
			$argc = 2;
		}
		if (static::getParam(QDB::CACHE) == QDB::ALLOWED) {
			$key = static::$cache->key('object', $argv);
			$res = static::$cache->cache($key);
			if ($res) {
				return $res;
			}
		}
		static::setParam(QDB::LIMIT, 1);
		$res = call_user_func_array(array('static', 'select'), $argv);
		static::setParam(QDB::LIMIT, 0);
		if ($res) {
			if (static::getParam(QDB::CACHE) == QDB::ALLOWED) {
				static::$cache->cache($key, $res[0]);
			}
			return $res[0];
		}
		if (static::getParam(QDB::CACHE) == QDB::ALLOWED) {
			static::$cache->cache($key, $res);
		}
		return $res;
	}
	public static function insertRow(array $data, $debug = false) {
		static::$cache->clear();
		$sql = QDBGenerator::insert(static::$config, static::$name, $data);
		if (static::query($sql, $data, $debug)) {
			return static::$db->lastInsertId();
		}
		return false;
	}
	// обновление записи в таблице БД
	public static function updateRow($sql, array $data, $debug = false) {
		static::$cache->clear();
		$sql = QDBGenerator::update(static::$config, static::$name, $data) . " " . $sql;
		if (static::query($sql, $data, $debug)) {
			return true;
		}
		return false;
	}
	// удаление записи из таблицы БД
	public static function deleteRow($sql, array $data, $debug = false) {
		static::$cache->clear();
		$table = static::$name;
		$sql = "delete from `$table` $sql limit 1";
		if (static::query($sql, $data, $debug)) {
			return true;
		}
		return false;
	}
	// получить количство записей в таблице БД
	public static function number() {
		$argc = func_num_args();
		$argv = func_get_args();
		if ($argc > 2) {
			trigger_error("QDBTable::number() should have 1, 2 or no arguments", E_USER_WARNING);
		}
		if ($argc == 1 && is_array($argv[0])) {
			$argv = $argv[0];
			$argc = count($argv);
		}
		if (@$argv[0] && is_string($argv[0]) && substr($argv[0], 0, 6) == "select") {
			static::setParam(QDB::PARSER, QDB::DENIED);
			$data = @$argv[1] ? $argv[1] : array();
			$res = static::query($argv[0], $data);
			$res = $res->fetch(PDO::FETCH_NUM);
			static::setParam(QDB::PARSER, QDB::ALLOWED);
			return $res[0];
		}
		$flag = null;
		$sql = '';
		$data = array();
		if ($argc == 1 && is_bool($argv[0])) {
			$flag = $argv[0];
		}
		if ($argc > 0 && is_string($argv[0])) {
			$sql = " $argv[0]";
		}
		if ($argc > 1 && is_array($argv[1])) {
			$data = $argv[1];
		}
		$table = static::$name;
		if ($flag === true) {
			static::setParam(QDB::SELECT, QDB::ALIVE);
		}
		if ($flag === false) {
			static::setParam(QDB::SELECT, QDB::DEAD);
		}
		$res = static::query("select count(*) from `$table`$sql", $data);
		if (is_bool($flag)) {
			static::setParam(QDB::SELECT, QDB::ALIVE);
		}
		$res = $res->fetch(PDO::FETCH_NUM);
		return $res[0];
	}
	public static function truncate() {
		$table = static::$name;
		return static::$db->exec("truncate table `$table`");
	}
}
?>