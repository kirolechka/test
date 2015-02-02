<?php
class ProtoArrayIterator implements ArrayAccess, Countable, Iterator, Serializable {
	protected $data = array();
	public function __construct(array $data = array()) { 
		$this->data = $data; 
	}
	public function offsetExists($offset) { 
		return isset($this->data[$offset]); 
	}
	public function offsetGet($offset) { 
		return isset($this->data[$offset]) ? $this->data[$offset] : null; 
	}
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			$this->data[] = $value;
		}
		else {
			$this->data[$offset] = $value;
		}
		return;
	}
	public function offsetUnset($offset) { 
		unset($this->data[$offset]); 
	}
	public function rewind() { 
		reset($this->data); 
	}
	public function current() { 
		return current($this->data); 
	}
	public function key() { 
		return key($this->data); 
	}
	public function next() { 
		next($this->data); 
	}
	public function valid() { 
		return isset($this->data[key($this->data)]); 
	}
	public function count() { 
		return count($this->data); 
	}
	public function serialize() { 
		return serialize($this->data); 
	}
	public function unserialize($data) { 
		$this->data = unserialize($data); 
	}
	public function __invoke(array $data = null) {
		if ($data === null) {
			return $this->data;
		}
		else {
			$this->data = $data;
		}
		return;
	}
}
?>