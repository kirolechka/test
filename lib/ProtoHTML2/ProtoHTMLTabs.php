<?php
// отвечает за создание табов
class ProtoHTMLTabs extends ProtoHTMLObject {
	public $ul;
	public function __construct($class = 'tabs') {
		$this->init('div');
		$this->class = $class;
		$this->ul = $this->append('ul');
		return;
	}
	public function tab($name, $id) {
		$this->ul
			->append('li')
			->append(ProtoHTML::a($name, "#$id"));
		return $this->append('div')
					->set('id', $id);
	}
}
?>