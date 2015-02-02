<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Core.php';
$Content = Core::$doc->content;
$Curr = CurrencyList::object(@$_GET['id']);
if (!$Curr) {
	echo "Валюта не найдена";
	die;
}
$Content->append(ProtoHTML::h1($Curr->name));
$Content->append(ProtoHTML::template('form.php', $Curr));
?>