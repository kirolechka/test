<?php
require_once 'TQDBObject.php';
class QDBObject extends QDBArray {
	use TQDBObject;
	protected $hash;
	protected $children;
	public function __construct (array $data = array()) {
		$this->data = $data;
		$this->checksum(true);
		$this->children = new QDBList();
		return;
	}
	public function __destruct() {
		return;
	}
	protected function checksum($set = false) {
		if ($set) {
			$this->hash = $this->hash();
			return;
		}
		return $this->hash == $this->hash();
	}
	public static function filter($name, $value, $type) {
		if ($value === null) {
			return $value;
		}
		$field = static::$config->$name;
		switch ($field["DATA_TYPE"]) {
			case "date":
				if ($type == "SET") {
					$res = date("Y-m-d", strtotime($value));
				}
				if ($type == "GET") {
					if ($value == "0000-00-00") {
						$res = "00.00.0000";
					}
					else {
						$res = date("d.m.Y", strtotime($value));
					}
				}
			break;
			default:
				$res = $value;
			break;
		}
		return $res;
	}
	public function __set($name, $value) {
		if ($name == static::$config->pri && static::$config->ai) {
			trigger_error('QDB: Fail to update primary field', E_USER_WARNING);
			return;
		}
		if (@static::$config->$name) {
			if (static::$htmlcode && $value && !in_array($name, static::$noStripFields)) {
				$value = htmlspecialchars_decode($value, ENT_QUOTES | ENT_HTML5);
			}
			$this->data[$name] = static::filter($name, $value, "SET");
			return;
		}
		trigger_error("QDB: Field '$name' is undeclared in structure", E_USER_WARNING);
		return;
	}
	public function __get($name) {
		$value = static::filter($name, $this->data[$name], "GET");
		if (static::$htmlcode && $value && !in_array($name, static::$noStripFields)) {
			return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
		}
		return $value;
	}
	public function __invoke(array $data = null) {
		if ($data === null) {
			$res = [];
			foreach ($this->data as $k => $v) {
				$res[$k] = $v;
			}
			return $res;
		}
		foreach ($data as $k => $v) {
			@$this->$k = $v;
		}
		return;
	}
	public function update($debug = false) {
		if ($this->checksum()) {
			return true;
		}
		$pri = static::$config->pri;
		if (@$this->$pri) {
			$data = $this();
			unset($data[$pri]);
			$data[':id'] = $this->$pri;
			$res = static::updateRow("where id = :id", $data, $debug);
			$new = static::object($this->$pri);
			$this($new());
			$this->checksum(true);
			return $res;
		}
		if (@$this->data[$pri] = static::insertRow($this(), $debug)) {
			$new = static::object($this->$pri);
			$this($new());
			$this->checksum(true);
			return true;
		}
		$this->checksum(true);
		return false;
	}
	protected function delete() {
		$pri = static::$config->pri;
		return static::deleteRow("where id = :id", array(':id' => $this->$pri));
	}
	public function children(QDBList $list = null) {
		if ($list === null) {
			return $this->children;
		}
		$this->children = $list;
		return;
	}
	public function export() {
		$res = $this->data;
		$res['children'] = $this->children
								->export();
		return $res;
	}
}
?>