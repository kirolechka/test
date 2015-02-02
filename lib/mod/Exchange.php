<?php
// класс курса валют
class Exchange extends QDBTable
{
    const ID = 'id';
    const COURSE = 'course';
    const CURRENCY_ID = 'currency_id';
    const BASE_CURRENCY_ID = 'base_currency_id';
    protected static $name = 'exchange';
	protected static function _validate(array $options) {
		if (!isset($options["base"])) {
			return 666;
		}
		if (!isset($options["target"])) {
			return 666;
		}
		if (!isset($options["course"])) {
			return 666;
		}
		if (!$options["base"] instanceof CurrencyList) {
			return 666;
		}
		if (!$options["target"] instanceof CurrencyList) {
			return 666;
		}
		if (!is_numeric($options["course"])) {
			return 666;
		}
		return null;
	}
    public static function create(array $options) {
    	$error = self::_validate($options);
    	if ($error) {
    		return $error;
    	}
    	$e = new self();
    	$e->currency_id = $options["target"]->id;
    	$e->base_currency_id = $options["base"]->id;
    	$e->course = $options["course"];
    	if (!$e->update()) {
    		return 5;
    	}
    	else {
    		return $e;
    	}
    }
    public function getBaseCurrency() { 
    	return CurrencyList::object($this->base_currency_id); 
    }
    public function getTargetCurrency() { 
    	return CurrencyList::object($this->currency_id); 
    }
} 
?>