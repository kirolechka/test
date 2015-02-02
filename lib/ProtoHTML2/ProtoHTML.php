<?php
// библиотека предназначена для генерации HTML5 документа, работы с DOM
// основным классом библиотеки ProtoHTML2 является класс ProtoHTML
// библиотека реализует полностью объектную структуру документа на стороне PHP
require_once 'ProtoHTMLDocument.php';
require_once 'ProtoHTMLChildren.php';
require_once 'ProtoHTMLObject.php';
require_once 'ProtoHTMLData.php';
require_once 'ProtoHTMLComment.php';
require_once 'ProtoHTMLMenu.php';
require_once 'ProtoHTMLBreadcrumbs.php';
require_once 'ProtoHTMLTabs.php';
require_once 'ProtoHTMLSelect.php';
require_once 'ProtoHTMLPaginator.php';
require_once 'ProtoHTMLPaginator2.php';
require_once 'TProtoHTMLParse.php';
require_once 'TProtoHTMLExtra.php';
class ProtoHTML {
	const TAG = 1;
	const ATTR = 2;
	use TProtoHTMLParse;
	use TProtoHTMLExtra;
	public static $singles = array ('br', 'hr', 'img', 'link', 'input', 'meta');
	public static $condSingles = array ('li', 'tr'); // p
	public static $preformatted = array ('pre');
	public static function document() {
		return new ProtoHTMLDocument();
	}
	public static function template() {
		if (func_num_args() == 0) {
			trigger_error('ProtoHTML::template() filename is not defined', E_USER_ERROR);
			return;
		}
		$args = func_get_args();
		$fn = $args[0];
		array_shift($args);
		if (!is_file($fn)) {
			trigger_error("Template file is not exist '$fn'", E_USER_WARNING);
			return false;
		}
		ob_start();
		include $fn;
		$html = ob_get_clean();
		$dom = new ProtoHTMLObject(null);
		self::parse($dom, $html);
		return $dom->children;
	}
	public static function validObject($obj) {
		if ($obj instanceof ProtoHTMLObject) {
			return true;
		}
		if ($obj instanceof ProtoHTMLChildren) {
			return true;
		}
		if ($obj instanceof ProtoHTMLData) {
			return true;
		}
		if ($obj instanceof ProtoHTMLComment) {
			return true;
		}
		return false;
	}
}
?>