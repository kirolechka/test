<?php
trait TProtoHTMLObjectArray {
	protected $pos;
	public function rewind() { 
		$this->pos = 0; 
	}
	public function current() { 
		return $this->children[$this->pos]; 
	}
	public function key() { 
		return $this->pos; 
	}
	public function next() { 
		++$this->pos; 
	}
	public function valid() { 
		return isset($this->children[$this->pos]); 
	}
	public function offsetSet($offset, $value) {
		if ($offset === NULL) {
			$this->children[] = $value;
		}
		else {
			$this->children[$offset] = $value;
		}
	}
	public function offsetExists($offset) { 
		return isset($this->children[$offset]); 
	}
	public function offsetUnset($offset) { 
		unset ($this->children[$offset]); 
	}
	public function offsetGet($offset) { 
		return isset($this->children[$offset]) ? $this->children[$offset] : NULL; 
	}
	public function count() { 
		return count($this->children); 
	}
}
?>