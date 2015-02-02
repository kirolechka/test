<?php
// отвечает за создание селектора
class ProtoHTMLSelect extends ProtoHTMLObject {
	public function __construct($name = null) {
		$this->init('select');
		if ($name) {
			$this->name = $name;
		}
		return;
	}
	public function option($value, $text, $selected = false) {
		$option = $this->append('option')
						->set('value', $value);
		$option->text($text);
		if ($selected) {
			$option->selected = true;
		}
		return $option;
	}
}
?>