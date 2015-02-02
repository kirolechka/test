<?php
require_once 'ProtoHTMLObject.php';
trait TProtoHTMLDocumentLink {
	public function css($url) {
		return $this->head
					->append(ProtoHTML::css($url));
	}
	public function js($url) {
		return $this->head
					->append(ProtoHTML::js($url));
	}
	public function dir($dir) {
		$this->head
			->append(ProtoHTML::dir($dir));
		return;
	}
	public function icon($url) {
		return $this->head
					->append(ProtoHTML::icon($url));
	}
}
class ProtoHTMLDocument {
	use TProtoHTMLDocumentLink;
	public $dom;
	public $html;
	public $head;
	public $body;
	public $title;
	public $charset = 'utf-8';
	public $autoBuild = true;
	public $autoPack = true;
	public $header;
	public $content;
	public $footer;
	public $mainmenu;
	public function __construct() {
		$this->dom = new ProtoHTMLObject(null);
		$this->dom
			->text('<!DOCTYPE html>');
		$this->html = $this->dom
							->append('html');
		$this->head = $this->html
							->append('head');
		$this->title = $this->head
							->append(new ProtoHTMLObject('title'));
		$this->body = $this->html
							->append('body');
		$this->head
			->append('meta')
			->set('charset', $this->charset);
	}
	public function __destruct() {
		if ($this->autoPack) {
			$this->pack();
		}
		if ($this->autoBuild) {
			echo $this;
		}
		return;
	}
	public function pack() {
		$this->dom
			->pack();
		return;
	}
	public static function checkSingle($tag) {
		$singles = explode(' ', self::SINGLE_TAGS);
		if (in_array($tag, $singles)) {
			return true;
		}
		return false;
	}
	public function __toString() {
		return (string) $this->dom;
	}
	public function dump($echo = true) {
		return $this->dom
					->dump($echo);
	}
	public function title($text) {
		$this->title
			->text($text);
		return;
	}
	public function sections() {
		$header = $this->dom
						->children('#HEADER');
		$content = $this->dom
						->children('#CONTENT');
		$footer = $this->dom
						->children('#FOOTER');
		$mainmenu = $this->dom
						->children('#MAINMENU');
		$rightSide = $this->dom
						->children('#RightSide');
		$this->header = ($header) ? $header : $this->body;
		$this->content = ($content) ? $content : $this->body;
		$this->footer = ($footer) ? $footer : $this->body;
		$this->mainmenu = ($mainmenu) ? $mainmenu : $this->body;
		$this->rightSide = ($rightSide) ? $rightSide : $this->body;
		return;
	}
	public function template($fn) {
		$this->body
			->append(ProtoHTML::template($fn));
		$this->sections();
	}
}
?>