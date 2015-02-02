<?php
require_once __DIR__ . '/../QCache.php';
require_once __DIR__ . '/../SQL.php';
class QDBConfig {
	private $class;
	private $data = array();
	public function __construct ($class) {
		$this->class = $class;
		$cacheName = $class::getDB()->name . $class::getTableName() . "config";
		if ($this->data = QCache::cache($cacheName)) {
			return;
		}
		$this->__read();
		$this->__parse();
		QCache::cache($cacheName, $this->data);
		return;
	}
	private function __read() {
		$class = $this->class;
		$sql = "select * from INFORMATION_SCHEMA.COLUMNS where TABLE_SCHEMA = :db and TABLE_NAME = :table";
		$data = array();
		$data[':db'] = $class::getDB()->name;
		$data[':table'] = $class::getTableName();
		$q = $class::getDB()->exec($sql, $data);
		if (!$q) {
			trigger_error("QDB: could not read structure of table `{$class->getDB()->name}`.`{$class->getTableName()}`", E_USER_ERROR);
		}
		$structure = $q->fetchAll(PDO::FETCH_ASSOC);
		foreach ($structure as $k => $v) {
			$this->data['structure'][$v['COLUMN_NAME']] = $v;
		}
		return;
	}
	private function __parse() {
		foreach ($this->data['structure'] as $k => $field) {
			switch($field['COLUMN_KEY']) {
				case 'PRI': 
					$this->data['pri'] = $k; 
				break;
				case 'UNI': 
					$this->data['uni'][]= $k; 
				break;
				case 'MUL': 
					$this->data['mul'][] = $k; 
				break;
			}
			if ($field['EXTRA'] == 'auto_increment') {
				$this->data['ai'] = true;
			}
			if ($k == 'deleted') {
				$this->data['del'] = true;
			}
		}
		return;
	}
	public function __get($name) {
		if ($name == 'pri' || $name == 'uni' || $name == "mul" || $name == 'ai' || $name == 'del') {
			return $this->data[$name];
		}
		if (isset($this->data['structure'][$name])) {
			return $this->data['structure'][$name];
		}
		trigger_error("QDB: field '$name' is undefined in structure of table", E_USER_WARNING);
		return null;
	}
	public function __invoke($type = QDB::ALL) {
		$res = array();
		switch ($type) {
			case QDB::ALL:
				$res = $this->data['structure'];
			break;
			case QDB::KEYS:
				foreach ($this->data['structure'] as $k => $v) {
					$res[] = $k;
				}
			break;
		}
		return $res;
	}
	private function __deadCondition() {
		$class = $this->class;
		if (@$this->deleted && $this->deleted['COLUMN_TYPE'] == 'tinyint(1)') {
			return "`{$class::getTableName()}`.`deleted` = 1";
		}
		if (@$this->deleted && $this->deleted['DATA_TYPE'] == 'int') {
			return "`{$class::getTableName()}`.`deleted` != 0";
		}
		if (@$this->deleted && $this->deleted['DATA_TYPE'] == 'timestamp') {
			return "`{$class::getTableName()}`.`deleted` is not null";
		}
		return null;
	}
	private function __aliveCondition() {
		$class = $this->class;
		if (@$this->deleted && $this->deleted['COLUMN_TYPE'] == 'tinyint(1)') {
			return "`{$class::getTableName()}`.`deleted` = 0";
		}
		if (@$this->deleted && $this->deleted['DATA_TYPE'] == 'int') {
			return "`{$class::getTableName()}`.`deleted` = 0";
		}
		if (@$this->deleted && $this->deleted['DATA_TYPE'] == 'timestamp') {
			return "`{$class::getTableName()}`.`deleted` is null";
		}
		return null;
	}
	private function __archiveSQL() {
		if (@$this->deleted && $this->deleted['COLUMN_TYPE'] == 'tinyint(1)') {
			return "update set deleted = 1";
		}
		if (@$this->deleted && $this->deleted['DATA_TYPE'] == 'int') {
			return "update set deleted = '" . time() . "'";
		}
		if (@$this->deleted && $this->deleted['DATA_TYPE'] == 'timestamp') {
			return "update set deleted = '" . date("Y-m-d H:i:s") . "'";
		}
		return null;
	}
	public function parseSQL($sql) {
		$class = $this->class;
		$sql = new SQL($sql);
		if ($class::getParam(QDB::PARSER) == QDB::DENIED) {
			return $sql;
		}
		if ($sql->select !== null) {
			switch ($class::getParam(QDB::SELECT)) {
				case QDB::ALIVE:
					$sql->andCondition($this->__aliveCondition());
				break;
				case QDB::DEAD:
					$sql->andCondition($this->__deadCondition());
				break;
			}
		}
		if ($sql->delete !== null && $class::getParam(QDB::DELETE) == QDB::ARCHIVE) {
			$newsql = $this->__archiveSQL();
			if ($newsql) {
				$newsql = new SQL($newsql);
				$newsql->update = $sql->from;
				$newsql->where = $sql->where;
				$sql = $newsql;
			}
		}
		return $sql;
	}
	public function genAllFields() {
		$res = "";
		foreach ($this->data['structure'] as $field => $val) {
			if ($res) {
				$res .= ", ";
			}
			$res .= "`$field`";
		}
		return $res;
	}
}
?>