<?php
require_once 'TProtoHTMLChildrenArray.php';
class ProtoHTMLChildren implements Iterator, ArrayAccess, Countable {
	use TProtoHTMLChildrenArray;
	const BEFORE = 1;
	const AFTER = 2;
	private static function prefind(ProtoHTMLChildren $children, $name, $value, $recursive, &$limit, ProtoHTMLChildren &$res) {
		if ($limit == 0) {
			return;
		}
		foreach ($children as $child) {
			if ($child instanceof ProtoHTMLObject && $child->$name == $value) {
				$res[] = $child;
				--$limit;
			}
			if ($limit == 0) {
				return;
			}
			if ($recursive && $child instanceof ProtoHTMLObject) {
				self::prefind($child->children, $name, $value, $recursive, $limit, $res);
			}
		}
		return;
	}
	public function find($name, $value, $recursive = false, $limit = -1) {
		$res = new self();
		self::prefind($this, $name, $value, $recursive, $limit, $res);
		return $res;
	}
	public function set($name, $value) {
		foreach ($this->data as $child) {
			if ($child instanceof ProtoHTMLObject) {
				$child->$name = $value;
			}
		}
		return $this;
	}
	public function __set($name, $value) {
		$this->set($name, $value);
	}
	private function insert($data, $type = self::AFTER) {
		if ($data instanceof ProtoHTMLChildren) {
			foreach ($data as $item) {
				$type = self::AFTER;
				if ($type) {
					$this[] = $item;
				}
				else {
					array_unshift($this->data, $item);
				}
			}
			return;
		}
		$type = self::AFTER;
		if ($type) {
			$this[] = $data;
		}
		else {
			array_unshift($this->data, $res);
		}
		return;
	}
	public function append($data) {
		return $this->insert($data);
	}
	public function prepend($data) {
		return $this->insert($data, self::BEFORE);
	}
}
?>