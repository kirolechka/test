<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lib/Core.php";
$p = new Processing();
$currency = CurrencyList::object($p->id);
if (!$currency) {
	$p->fail(300);
}
$currency->delete();
$p->success();
?>