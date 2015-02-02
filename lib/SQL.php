<?php
require_once 'QCache.php';

class SQL {
	private $data = [];
	private static $keys = ['select', 'insert', 'update', 'delete', 'where', 'limit', 'set', 'from', 'order', 'values', 'into'];
	private static $quotes = '"`\'';
	private static $separators = " \r\n\t(){}[],.=<>";
	public function __construct($sql) {
		if ($this->data = QCache::cache($sql)) {
			return;
		}
		$this->explode($sql);
		QCache::cache($sql, $this->data);
		return;
	}
	private function check($sql, $pos) {
		foreach (self::$keys as $key) {
			if (strtolower(substr($sql, $pos, strlen($key))) == $key
			&& ($pos == 0
			|| strpos(self::$separators, $sql[$pos - 1]) !== false)
			&& (($pos + strlen($key)) >= strlen($sql)
			|| strpos(self::$separators, $sql[$pos + strlen($key)]) !== false)) {
				return $key;
			}
		}
		return;
	}
	private function parseQuote($key, $sql, &$pos) {
		if (false === strstr(self::$quotes, $sql[$pos])) {
			return;
		}
		$quote = $sql[$pos];
		$end = strpos($sql, $quote, $pos);
		$end = ($end === false) ? null : $end;
		@$this->data[$key] .= substr($sql, $pos, $end);
		$pos = ($end === null) ? $pos = strlen($sql) : $pos += $end;
		return;
	}
	private function parseKey(&$current, $sql, &$pos) {
		$key = $this->check($sql, $pos);
		if (!$key) {
			return false;
		}
		$current = $key;
		$pos += strlen($key);
		return true;
		return;
	}
	private function parseVal($current, $sql, &$pos) {
		while ($pos < strlen($sql) && !$this->check($sql, $pos)) {
			@$this->data[$current] .= $sql[$pos];
			++$pos;
		}
		return;
	}
	private function explode($sql) {
		$pos = 0;
		$current = '';
		while ($pos < strlen($sql)) {
			$this->parseQuote($current, $sql, $pos);
			if ($this->parseKey($current, $sql, $pos)) {
				$this->parseVal($current, $sql, $pos);
			}
			else {
				@$this->data[$current] .= $sql[$pos];
				++$pos;
			}
		}
	}
	private function trim() {
		foreach ($this->data as $k => $v) {
			$this->data[$k] = trim($v);
		}
		return;
	}
	private function implode() {
		$res = '';
		if (!$this->data) {
			return $res;
		}
		$data = $this->data;
		$order = @$data['order'];
		$limit = @$data['limit'];
		unset($data['order']);
		unset($data['limit']);
		foreach ($data as $k => $v) {
			if ($k && $res) {
				$res .= " ";
			}
			if ($k) {
				$res .= $k;
			}
			if ($k && $v) {
				$res .= " ";
			}
			if ($v) {
				$res .= $v;
			}
		}
		if ($order) {
			$res .= " order $order";
		}
		if ($limit) {
			$res .= " limit $limit";
		}
		return $res;
	}
	public function __toString() {
		return $this->implode();
	}
	public function __set($name, $value) {
		@$this->data[$name] = " $value ";
		return;
	}
	public function __get($name) {
		if (!isset($this->data[$name])) {
			return null;
		}
		return $this->data[$name];
	}
	public function andCondition($condition) {
		if (!$condition) {
			return;
		}
		$this->where = $this->where ? "({$this->where}) and $condition" : $condition;
		return;
	}
	public function orCondition($condition) {
		if (!$condition) {
			return;
		}
		$this->where = $this->where ? "{$this->where} or $condition" : $condition;
		return;
	}
}
?>