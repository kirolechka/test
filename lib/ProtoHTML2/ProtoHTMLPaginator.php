<?php
class ProtoHTMLPaginator extends ProtoHTMLObject {
	public $page;
	public $number;
	public $offset;
	public $rows = 20;
	private $prev;
	private $next;
	private $max;
	private $url;
	private $interval = 5;
	private $postfix;
	private $plain = 10;
	private $size = 40;
	private function __transformation($x, $pa, $pb, $a, $b) {
		if ($x == 1) {
			return 1;
		}
		if ($x == $this->size) {
			return $this->max;
		}
		if ($x >= $a && $x <= $b) {
			return $x + $pa - $a;
		}
		if ($x < $a) {
			return (int) ($pa / $a * $x);
		}
		if ($x > $b) {
			return (int) (($this->max - $pb) / ($this->size - $b) * ($x - $b) + $pb);
		}
	}
	private function __generateTransformationArray() {
		$fa = (int) $this->page - $this->plain / 2 + 1;
		$fb = (int) $this->page + $this->plain / 2;
		if ($fa < 1) {
			$fa = 1;
		}
		if ($fb > $this->max) {
			$fb = $this->max;
		}
		$step = ($this->max - $fb + $fa) / $this->size;
		$a = ($this->size - $this->plain) / 2;
		$b = ($this->size + $this->plain) / 2;
		if ($fa - ($this->size - $this->plain) / 2 < 1) {
			$a = $fa == 1 ? 1 : 2;
			$b = $a + $this->plain;
		}
		if ($fb + ($this->size - $this->plain) / 2 > $this->max) {
			$b = $this->size;
			$a = $b - $this->plain;
		}
		if (($b - $a) > ($fb - $fa)) {
			if ($a == 1 || $a == 2) {
				$b = $a + $fb - $fa;
			}
			if ($b == $this->size) {
				$a = $b - $fb + $fa;
			}
		}
		$res = [];
		for ($x = 1; $x <= $this->size; $x++) {
			$y = $this->__transformation($x, $fa, $fb, $a, $b);
			$res[$x] = $y;
		}
		return $res;
	}
	public function __construct($number, $postfix = '') {
		$this->init('ul');
		$this->class = 'paginator';
		$this->number = $number;
		$this->postfix = $postfix;
		$this->__prepare();
		$this->append($this->__genScript());
		$this->__generate();
		return;
	}
	private function __genScript() {
		$script = new ProtoHTMLObject('script');
		$script->text("
				$(document).ready(function(){
					$('.paginator select').change(function(){
						location = '{$this->url}page=' + $(this).val() + '{$this->postfix}';
					});
				});
				");
		return $script;
	}
	private function __prepare() {
		$this->__prepareURL();
		$this->max = (int) ($this->number / $this->rows);
		if ($this->number % $this->rows > 0) {
			++$this->max;
		}
		if ($this->number == 0) {
			$this->max = 1;
		}
		$this->page = @$_GET['page'] ? (int) $_GET['page'] : 1;
		$this->page = $this->__validate($this->page);
		$this->prev = $this->__validate($this->page - 1);
		$this->next = $this->__validate($this->page + 1);
		$this->offset = ($this->page - 1) * $this->rows;
		return;
	}
	private function __prepareURL() {
		$this->url = $_SERVER['REQUEST_URI'];
		if (strpos($this->url, '?') === false) {
			$this->url .= '?';
			return;
		}
		$start = strpos($this->url, 'page=');
		if ($start === false) {
			$this->url .= '&';
			return;
		}
		if ($start > 0 && $this->url[$start - 1] == '&') {
			--$start;
		}
		$end = strpos($this->url, '&', $start + 1);
		$end = ($end === false) ? strlen($this->url) : $end;
		$this->url = substr($this->url, 0, $start) . substr($this->url, $end) . '&';
		return;
	}
	private function __validate($number) {
		if ($number < 1) {
			return 1;
		}
		if ($number > $this->max) {
			return $this->max;
		}
		return $number;
	}
	private function __generate() {
		$pos = $this->__validate($this->page - $this->interval);
		$maxpos = $this->__validate($this->page + $this->interval);
		$this->append('li')
			->append(ProtoHTML::a(' « ', $this->__genURL(1)));
		$this->append('li')
			->append(ProtoHTML::a(' ‹ ', $this->__genURL($this->prev)));
		if ($pos > 1) {
			$this->append('li')
				->text('...');
		}
		for ($pos; $pos <= $maxpos; $pos++) {
			$li = $this->append('li');
			$li->append(ProtoHTML::a($pos, $this->__genURL($pos)));
			if ($pos == $this->page) {
				$li->class = "active";
			}
		}
		if ($maxpos < $this->max) {
			$this->append('li')
				->text('...');
		}
		$this->append('li')
			->append(ProtoHTML::a(' › ', $this->__genURL($this->next)));
		$this->append('li')
			->append(ProtoHTML::a(' » ', $this->__genURL($this->max)));
		$selector = new ProtoHTMLSelect('page');
		$pages = $this->__generateTransformationArray();
		foreach ($pages as $page) {
			if ($page == $this->page) {
				$selector->option($page, $page, true);
			}
			else {
				$selector->option($page, $page);
			}
		}
		$this->append('li')
			->append($selector);
		return;
	}
	private function __genURL($page) {
		return "{$this->url}page=$page{$this->postfix}";
	}
}
?>