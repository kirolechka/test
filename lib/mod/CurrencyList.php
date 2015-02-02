<?php
// класс валют
class CurrencyList extends QDBTable {
	protected static $name = 'currency_list';
	public static function create(array $options) {
		$curr = new self();
		$curr->number = $options['number'];
		$curr->code = $options['code'];
		$curr->name = $options['name'];
		$error = $curr->update();
		if ($error) {
			return $error;
		}
		return $curr;
	}
	public static function select() {
		$argc = func_num_args();
		$argv = func_get_args();
		if ($argc == 1 && (ctype_upper($argv[0]))) {
			return parent::select("where code = ? order by id", [$argv[0]]);
		}
		if ($argc == 0) {
			return parent::select();
		}
		return parent::select($argv);
	}
	// вывод $value в соответствии с валютным обозначением
	public function format($value) {
		return number_format($value, 2, '.', ' ') . ' ' . $this->code;
	}
	public function toString() {
		return $this->code;
	}
	protected function _validate() {
		if ($this->number < 0 || $this->number > 999) {
			return 666;
		}
		if (strlen($this->code) < 2 || strlen($this->number) > 3) {
			return 666;
		}
		return;
	}
	public function update($debug = false) {
		if ($this->checksum()) {
			return null;
		}
		$error = $this->_validate();
		if ($error) {
			return $error;
		}
		if (!parent::update($debug)) {
			return 5;
		}
		return null;
	}
}
?>