<?
$serverRoot = $_SERVER['DOCUMENT_ROOT'];
$corePath = $serverRoot . '/lib/Core.php';
require_once $corePath;

$_POST[Exchange::COURSE] = str_replace(',', '.', $_POST[Exchange::COURSE]);
$processing = new Processing();

$course = $_POST[Exchange::COURSE];
$courseIsNumeric = is_numeric($course);
if (!$courseIsNumeric) {
	$processing->fail(1301);
}

$baseCurrencyId = $_POST[Exchange::BASE_CURRENCY_ID];
$baseCurrency = CurrencyList::object($baseCurrencyId);
if (!$baseCurrency) {
	$processing->fail(1303 . $baseCurrencyId);
}

$currencyId = $_POST[Exchange::CURRENCY_ID];
$currency = CurrencyList::object($currencyId);
if (!$currency) {
	$processing->fail(1302 . $currencyId);
}

if ($currencyId == $baseCurrencyId) {
	$processing->fail(1304);
}

$result = Exchange::revise($baseCurrencyId, $currencyId);
if ($result) {
	$processing->fail(1305);
}

$exchange = Exchange::create($processing());
$processing->success(['exchange' => $exchange]);
?>