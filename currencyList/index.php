<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Core.php';
$Content = Core::$doc->content;
$Content->append(ProtoHTML::h1('Валюты'));
$CurrList = CurrencyList::select();
$Content->append(ProtoHTML::template('table.php', $CurrList));
$Content->append(ProtoHTML::h2('Добавить валюту'));
$Content->append(ProtoHTML::template('create.php'));
?>