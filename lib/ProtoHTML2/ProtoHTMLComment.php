<?php
class ProtoHTMLComment {
	private $data;
	public function __construct($data) {
		$this->data = $data;
		return;
	}
	public function __toString() {
		return "<!--{$this->data}-->";
	}
	public function append($data) {
		$this->data .= $data;
		return $this;
	}
	public function prepend($data) {
		$this->data = $data . $this->data;
		return $this;
	}
}
?>