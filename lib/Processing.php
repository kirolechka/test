<?php
// свойства класса совпадают с переменными, передаваемыми в процессинговый скрипт через POST и GET
// но, получить доступ к этим свойствам можно только один раз
// после этого, они обнуляются
// сделано это для того, чтобы лишние данные не попадали дальше в процессинг или библиотеки и модули
class Processing {
	const JSON = 1;
	const XML = 2;
	const PLAIN = 3;
	const REDIRECT = 4;
	const NONE = 5;
	private $type;
	private $error;
	private $errorCode;
	private $data;
	private $failURL;
	private $successURL;
	private $backURL;
	// создает объект класса Processing 
	// в качестве аргумента задается тип работы процессинга и возвращаемых им данных 
	// если аргумент не задан, по-умолчанию устанавливается тип Processing::JSON
	public function __construct($type = self::JSON) {
		Core::$doc->autoBuild = false;
		$this->type = $type;
		$this->successURL = $this->SUCCESS_URL;
		$this->failURL = $this->FAIL_URL;
		$this->backURL = $this->BACK_URL;
		if (!$this->backURL) {
			$this->backURL = @$_SERVER['HTTP_REFERER'];
		}
		if (!$this->successURL) {
			$this->successURL = $this->backURL;
		}
		if (!$this->failURL) {
			$this->failURL = $this->backURL;
		}
		return;
	}
	public function __destruct() {
		switch ($this->type) {
			case self::NONE: 
			break;
			case self::JSON: 
				$this->json(); 
			break;
			case self::XML: 
				$this->xml(); 
			break;
			case self::PLAIN: 
				$this->plain(); 
			break;
			case self::REDIRECT: 
				$this->redirect(); 
			break;
		}
		return;
	}
	private function json() {
		if ($this->errorCode) {
			$res = array(
					'result' => 'fail',
					'error' => array(
							'code' => $this->errorCode,
							'msg' => $this->error,
					),
			);
		}
		else {
			$res = array(
					'result' => 'success',
					'data' => $this->data,
			);
		}
		echo json_encode($res);
		return;
	}
	private function plain() {
		echo "#{$this->errorCode}: {$this->error}";
		return;
	}
	private function redirect() {
		if ($this->error) {
			if ($this->failURL) {
				header('location: ' . $this->failURL);
			}
			else {
				header('location: ' . $this->backURL);
			}
			return;
		}
		if ($this->successURL) {
			header('location: ' . $this->successURL);
		}
		else {
			header('location: ' . $this->backURL);
		}
		return;
	}
	public function fail($msg = "Внутренняя ошибка") {
		$this->errorCode = '000';
		if (!is_string($msg)) {
			$this->errorCode = (int) $msg;
			if (!is_int($msg)) {
				$this->errorCode = 4;
			}
			$msg = ErrorCodes::get($this->errorCode);
		}
		$this->error = $msg;
		die;
	}
	// инициализирует соответствующий механизм успешной остановки процессинга (зависит от типа, заданного в конструкторе)
	// если передан аргумент, то он будет передан по возможности сценарию, запустившему процессинг
	public function success($data = null) {
		if (is_object($data)) {
			$data = $data();
			foreach ($data as $k => $v) {
				if (is_object($v))  {
					$data[$k] = $v();
				}
			}
		}
		$this->data = $data;
		die;
	}
	// ищет по заданному имени данные в $_POST и $_GET, возвращает их и обнуляет соответствующую запись в $_POST или $_GET
	public function get($name) {
		if (isset($_GET[$name])) {
			$res = $_GET[$name];
			unset($_GET[$name]);
			return $res;
		}
		if (isset($_POST[$name])) {
			$res = $_POST[$name];
			unset($_POST[$name]);
			return $res;
		}
		return null;
	}
	// метод аналогичен по своей работе Processing::get за исключением того,
	// что он возвращает логической значение: true - данные существуют, false - нет
	// также происходит обнуление
	public function check($name) {
		if ($this->$name === null) {
			return false;
		}
		return true;
	}
	public function __get($name) {
		return $this->get($name);
	}
	public function __invoke() {
		return array_merge($_GET, $_POST);
	}
}
?>