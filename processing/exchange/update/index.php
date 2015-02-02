<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Core.php';
$p = new Processing();
// получение курса по коду валюты
function getValueByCurrencyCode (DOMDocument $doc, $num) {
	$nodes = $doc->getElementsByTagName("NumCode");
	foreach ($nodes as $node) {
		if ($node->nodeValue == $num) {
			return $node->parentNode
						->getElementsByTagName("Value")
						->item(0)
						->nodeValue;
		}
	}
}

$doc = new DOMDocument();
$doc->load("http://www.cbr.ru/scripts/XML_daily.asp");
// получение валют из БД
$currencyList = CurrencyList::select();
$target = CurrencyList::object('RUB');
foreach ($currencyList as $k => $curr) {
	if ($curr->code == 'RUB') {
		continue;
	}
	$value = getValueByCurrencyCode($doc, $curr->number);
	if (!$value) {
		continue;
	}
	
	$value = str_replace(",", ".", $value);
	$value = (double) $value;
	$data = [':base' => $curr->id, ':target' => $target->id];
	$sql = "where currency_id = :target and base_currency_id = :base";
	$ex = Exchange::object($sql, $data);
	// если курс есть в БД, то обновить его
	// иначе создать новую запись
	if ($ex) {
		$ex->course = $value;
		$ex->update();
	}
	else {
		$e = Exchange::create(["base"=> $curr,	"target" => $target, "course" => $value]);		
	}
}
$p->success();
?>