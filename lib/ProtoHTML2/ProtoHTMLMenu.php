<?php
// отвечает за создание меню
class ProtoHTMLMenu extends ProtoHTMLObject {
	public function __construct($class = null) {
		$this->init('ul');
		if ($class) {
			$this->class = $class;
		}
		return;
	}
	public function separator() {
		return $this->append('li')
					->set('class', 'separator');
	}
	public function item($text, $url='') {
		$li = $this->append('li');
		$li->append(ProtoHTML::a($text, $url));
		return $li;
	}
	public function submenu($text, $url='') {
		return $this->item($text, $url)
					->append(ProtoHTML::menu());
	}
}
?>