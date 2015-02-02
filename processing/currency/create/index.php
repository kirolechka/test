<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Core.php';
$p = new Processing();
$currency = CurrencyList::create($p());
if (!$currency) {
	$p->fail('Не удалось создать валюту');
}
$p->success(array('currency' => $currency->id));
?>