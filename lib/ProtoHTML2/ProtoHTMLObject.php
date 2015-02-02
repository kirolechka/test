<?php
require_once 'TProtoHTMLDump.php';
require_once 'TProtoHTMLObject.php';
require_once 'TProtoHTMLObjectArray.php';
class ProtoHTMLObject implements Iterator, ArrayAccess, Countable {
	use TProtoHTMLObjectArray;
	use TProtoHTMLDump;
	use TProtoHTMLObject;
	protected $tag;
	protected $children;
	protected $attr = array();
	protected $single;
	protected $condSingle;
	protected $preformatted;
	public function __construct($tag) {
		$this->init($tag);
		return;
	}
	public function init($tag) {
		$this->tag = $tag;
		$this->children = new ProtoHTMLChildren();
		$this->single = in_array($this->tag, ProtoHTML::$singles);
		$this->condSingle = in_array($this->tag, ProtoHTML::$condSingles);
		$this->preformatted = in_array($this->tag, ProtoHTML::$preformatted);
		return;
	}
	public function single() {
		return $this->single;
	}
	public function preformatted() {
		return $this->preformatted;
	}
	public function condSingle() {
		return $this->condSingle;
	}
	public function __toString() {
		$res = '';
		if ($this->tag) {
			$res = "<{$this->tag}{$this->buildAttrs()}>";
		}
		foreach ($this as $child) {
			$res .= $child;
		}
		if ($this->tag && !$this->single() && !$this->condSingle()) {
			$res .= "</{$this->tag}>";
		}
		return $res;
	}
	protected function buildAttrs() {
		$res = '';
		foreach ($this->attr as $name => $value) {
			$attr = $this->buildAttr($name, $value);
			if ($attr) {
				$res .= ' ' . $attr;
			}
		}
		return $res;
	}
	protected function buildAttr($name, $value) {
		if ($value === NULL) {
			return;
		}
		if ($value === false) {
			return;
		}
		if ($value === true) {
			return "$name";
		}
		if (is_int($value)) {
			return "$name=$value";
		}
		if (is_object($value)) {
			return "$name=\"{$value->build()}\"";
		}
		return "$name=\"$value\"";
	}
	public function __set($name, $value) {
		return $this->set($name, $value);
	}
	public function __get($name) {
		return $this->get($name);
	}
	public function __call($name, $args) {
		$children = $this->find('tag', $name);
		if (count($args) == 1 && is_int($args[0])) {
			return $children[$args[0]];
		}
		if (count($args) == 2 && is_string($args[0])) {
			return $children->find($args[0], $args[1]);
		}
		return $children;
	}
	public function set($name, $value) {
		$this->attr[$name] = $value;
		return $this;
	}
	public function get($name) {
		if ($name == 'children') {
			return $this->children;
		}
		if ($name == 'tag') {
			return $this->tag;
		}
		if (isset($this->attr[$name])) {
			return $this->attr[$name];
		}
		$children = $this->find('tag', $name, false);
		if (count($children) == 1) {
			return $children[0];
		}
		return $children;
	}
	public function append($obj) {
		if (!$obj) {
			return null;
		}
		if (is_string($obj)) {
			$obj = new ProtoHTMLObject($obj);
		}
		$this->children
			->append($obj);
		return $obj;
	}
	public function prepend($obj) {
		if (is_string($obj)) {
			$obj = new ProtoHTMLObject($obj);
		}
		$this->children
			->prepend($obj);
		return $obj;
	}
	public function text($text) {
		$obj = new ProtoHTMLData($text);
		$this->append($obj);
		return $this;
	}
	public function find($name, $value, $recursive = false, $limit = -1) {
		return $this->children
					->find($name, $value, $recursive, $limit);
	}
	public function pack() {
		if ($this->preformatted) {
			return;
		}
		if (!count($this->children)) {
			return;
		}
		foreach ($this->children as $k => $child) {
			if ($child instanceof ProtoHTMLObject || $child instanceof ProtoHTMLData) {
				$child->pack();
			}
			if ($child instanceof ProtoHTMLComment) {
				unset($this->children[$k]);
			}
		}
		return;
	}
	public function children($selector) {
		if (!is_string($selector)) {
			return;
		}
		$children = $this->children;
		$tag = null;
		$id = null;
		$class = null;
		$name = null;
		$value = null;
		if ($selector[0] != '#' && $selector[0] != '.' && $selector[0] != '[') {
			$pos = false;
			$pos = strpos($selector, '#');
			$pos = ($pos === false) ? strpos($selector, '.') : $pos;
			$pos = ($pos === false) ? strpos($selector, '[') : $pos;
			$tag = ($pos === false) ? $selector : substr($selector, 0, $pos);
			$selector = ($pos === false) ? null : substr($selector, $pos);
		}
		if ($tag) {
			$children = $children->find('tag', $tag);
		}
		if (!$selector) {
			return $children;
		}
		if ($selector[0] == '#') {
			return $children->find('id', substr($selector, 1), true, 1)[0];
		}
		if ($selector[0] == '.') {
			$pos = false;
			$pos = strpos($selector, '[');
			$class = ($pos === false) ? $selector : substr($selector, 0, $pos);
			$selector = ($pos === false) ? null : substr($selector, $pos + 1);
		}
		if ($class) {
			$children = $children->find('class', $class);
		}
		if (!$selector) {
			return $children;
		}
		$pos = strpos($selector, '=');
		$name = ($pos === false) ? substr($selector, 1, strlen($selector) - 2) : substr($selector, 1, $pos - 1);
		$selector = ($pos === false) ? null : substr($selector, $pos + 1);
		$pos = ($selector) ? strpos($selector, ']') : false;
		$value = ($pos === false) ? true : substr($selector, 0, $pos);
		$children = $children->find($name, $value);
		return $children;
	}
}
?>