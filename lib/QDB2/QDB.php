<?php
require_once 'QDBArray.php';
require_once 'QDBTable.php';
// класс QDB является наследником класса PDO библиотеки PDO, как следствие обладает теми же свойствами и методами
class QDB extends PDO {
	const ALL = 1;
	const KEYS = 2;
	const ALLOWED = 3;
	const DENIED = 4;
	const PARSER = 5;
	const SELECT = 6;
	const DEAD = 7;
	const ALIVE = 8;
	const DELETE = 9;
	const ABSOLUTE = 10;
	const ARCHIVE = 11;
	const DELETE_UPDATE = "update {table} where id = {pri} set deleted = {unixtime}";
	const LIMIT = 12;
	const CACHE = 13;
	public $debug = false;
	private $config;
	private $log = array();
	// конструктор класса
	public function __construct($config) {
		if (is_string($config) && is_file($config)) {
			$config = include $config;
		}
		if (!is_array($config)) {
			return;
		}
		$this->config = $config;
		$dsn = "{$this->config['driver']}:dbname={$this->config['name']};
				host={$this->config['host']};
				charset={$this->config['charset']}";
		try {
			parent::__construct($dsn, $this->config['user'], $this->config['pass']);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			trigger_error("QDB Error: " . $e->getMessage(), E_USER_ERROR);
		}
		return;
	}
	// псевдоним механизма prepare/execute
	public function exec($sql, $data = array(), $debug = false) {
		$this->log("$sql", $data);
		try {
			$q = $this->prepare("$sql");
			$q->execute($data);
		}
		catch (PDOException $e) {
			if ($this->debug || $debug) {
				var_dump($this->log[count($this->log) - 1]);
				echo $e->getMessage();
			}
			trigger_error("QDB::query() SQL Error: {$e->getMessage()}", E_USER_ERROR);
		}
		return $q;
	}
	public function log($sql = null, $data = null) {
		if ($sql === null && $data === null) {
			return $this->log;
		}
		$this->log[] = array(
				'sql' => $sql,
				'data' => $data,);
		return;
	}
	public function __get($name) {
		switch ($name) {
			case "name": 
				return $this->config["name"]; 
			break;
			default: 
				trigger_error("QDB::$$name is undefined", E_USER_WARNING); 
				return null; 
			break;
		}
	}
}
?>