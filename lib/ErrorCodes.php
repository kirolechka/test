<?php
class ErrorCodes {
	private static $codes = [
		// COMMON ERRORS
		1 => ['Undefined error', 'Неизвестная ошибка', ],
		2 => ['Undefined error code', 'Неопределенный тип ошибки'],
		3 => ['Wrong query structure', 'Неверная структура запроса'],
		4 => ['Not numeric error code', 'Нечисловой код ошибки'],
		5 => ['DB error', 'Ошибка БД'],
		6 => ["File open error", "Ошибка чтения файла"],
		// CURRENCIES
		300 => ['Currency not found', 'Валюта не найдена'],
		// EXCHANGE
		1300 => ['Exchange not found', 'Обменный курс не найден'],
		1301 => ['The course must be a number', 'Курс должен быть числом'],
		1302 => ['Not found the currency with id ', 'Не найдена валюта с id '],
		1303 => ['Not found the base currency with id ', 'Не найдена базовая валюта с id '],
		1304 => ['Selected two of the same currency', 'Выбраны две одинаковые валюты'],
		1305 => ['Exchange rate with such currencies already exists', 'Обменный курс с такими валютами уже существует'],
		1306 => ['Failed to remove course', 'Не удалось удалить курс'],
		1307 => ['An error occurred while updating the course', 'Произошла ошибка при обновлении курса'],
		1308 => ['Could not find the exchange rate with the id ', 'Не удалось найти обменный курс с id '],
	];
	public static function get ($code, $lang = 0) {
		if (is_string($lang))
			switch ($lang) {
				case 'RU': $lang = 1; break;
				case 'EN': $lang = 0; break;
			}
		$lang = 0;
		if (ACL::$account) {
			switch (ACL::$account->lang) {
				case 'EN': $lang = 0; break;
				case 'RU': $lang = 1; break;
			}
		}
		if (isset(self::$codes[$code]))
			return self::$codes[$code][$lang];
		$code = 2;
		if (isset(self::$codes[$code]))
			return self::$codes[$code][$lang];
		trigger_error('ErrorCodes: inner error', E_USER_ERROR);
		die;
	}
}
?>