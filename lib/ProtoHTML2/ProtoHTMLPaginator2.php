<?php
class ProtoHTMLPaginator2 extends ProtoHTMLObject {
	public function __construct($page, $next = true) {
		$prev = $page - 1;
		$next = $next ? $page + 1 : 0;
		$this->init('div');
		$this->class = "paginator2";
		if ($prev > 0) {
			$this->append("a")
				->set("href", "./?page=$prev")
				->text("предыдущая");
			$this->text(" ");
		}
		$this->append("span")->text($page);
		if ($next > 0) {
			$this->text(" ");
			$this->append("a")
				->set("href", "./?page=$next")
				->text("следующая");
		}
		return;
	}
}
?>