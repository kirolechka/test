<?php
// отвечает за создание хлебных крошек
class ProtoHTMLBreadcrumbs extends ProtoHTMLObject {
	public function __construct($class = 'breadcrumbs') {
		$this->init('ul');
		$this->class = $class;
		return;
	}
	public function crumb($name, $url = null) {
		$li = $this->append('li');
		$li->append('a')->set('href', $url)->text($name);
		return $li;
	}
}
?>