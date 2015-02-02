<?php
trait TProtoHTMLChildrenArray {
	private $data = array();
	private $pos = 0;
	public function __construct($data = array()) { 
		$this->data = $data; 
	}
	public function rewind() { 
		$this->pos = 0; 
	}
	public function current() { 
		return $this->data[$this->pos]; 
	}
	public function key() { 
		return $this->pos; 
	}
	public function next() { 
		++$this->pos; 
	}
	public function valid() { 
		return isset($this->data[$this->pos]); 
	}
	public function offsetSet($offset, $value) {
		if ($offset === NULL) {
			$this->data[] = $value;
		}
		else {
			$this->data[$offset] = $value;
		}
	}
	public function offsetExists($offset) { 
		return isset($this->data[$offset]); 
	}
	public function offsetUnset($offset) { 
		unset ($this->data[$offset]); 
	}
	public function offsetGet($offset) { 
		return isset($this->data[$offset]) ? $this->data[$offset] : NULL; 
	}
	public function count() { 
		return count($this->data); 
	}
}
?>