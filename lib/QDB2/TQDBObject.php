<?php
trait TQDBObject {
	public function getParent() {
		if (!$this->parent_id) {
			return null;
		}
		$sql = "where id = :id";
		$data = array(':id' => $this->parent_id);
		return static::object($sql, $data);
	}
	public function isRoot() {
		if ($this->parent_id) {
			return false;
		}
		return true;
	}
	public function getRoot() {
		if ($this->isRoot()) {
			return $this;
		}
		return $this->getParent()
					->getRoot();
	}
	public function getChildren($recursive = false) {
		$children = static::select('where parent_id = ?', array($this->id));
		if (!$recursive) {
			return $children;
		}
		$res = new QDBList();
		$res->merge($children);
		foreach ($children as $child) {
			$res->merge($child->getChildren(true));
		}
		return $res;
	}
	public function hasChildren() {
		$sql = "where parent_id = :id";
		$data = array(':id' => $this->id);
		return (bool) static::object($sql, $data);
	}
	public function getTrunk($reversive = false) {
		$res = new QDBList(array($this));
		$parent = $this->getParent();
		if (!$parent) {
			return $res;
		}
		$res->merge($parent->getTrunk(true));
		if (!$reversive) {
			$res->reverse();
		}
		return $res;
	}
	public function getBranch() {
		$children = $this->getChildren();
		foreach ($children as $child) {
			$child->children($child->getBranch());
		}
		return $children;
	}
	public static function genSelect($name = 'id') {
		$select = ProtoHTML::select($name);
		foreach (static::select() as $obj) {
			$select->option($obj->id, $obj->name);
		}
		return $select;
	}
}
?>