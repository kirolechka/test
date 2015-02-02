<?php
class QDBList extends QDBArray {
	public function __construct (array $data = array()) {
		$this->data = $data;
		return;
	}
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			if ($value instanceof QDBList) {
				foreach ($value as $v) {
					$this->data[] = $v;
				}
			}
			else {
				$this->data[] = $value;
			}
		}
		else {
			$this->data[$offset] = $value;
		}
		return;
	}
	public function __set($name, $value) {
		foreach ($this as $obj) {
			$obj->$name = $value;
		}
		return;
	}
	// обновить записи, содержащиеся в списке
	public function update() {
		$res = true;
		foreach ($this as $obj)
			$res = $res && $obj->update();
		return $res;
	}
	// удалить записи, содержащиеся в списке
	public function delete() {
		$resBool = true;
		$resArr = [];
		foreach ($this as $obj) {
			$res = $obj->delete();
			if (is_bool($res)) {
				$resBool = $resBool && $res;
			}
			elseif ($res) {
				$resArr[] = $res;
			}
		}
		if (count($resArr)) {
			return $resArr;
		}
		return $resBool;
	}
	public function __call($name, $args) {
		$res = array();
		foreach ($this as $obj) {
			$res[] = call_user_func_array(array($obj, $name), $args);
		}
		return $res;
	}
	public function export() {
		$res = $this->data;
		foreach ($res as $k => $v) {
			$res[$k] = $v->export();
		}
		return $res;
	}
}
?>