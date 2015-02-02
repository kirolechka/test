<?php
class Logger {
	private $dir;
	private $log = array();
	private $time;
	private $micro;
	private $silence = false;
	public function __construct($dir, $micro = null) {
		$this->dir = $dir;
		$this->time = time();
		$this->micro = ($micro === null) ? microtime() : $micro;
		$this->log[] = date('r');
		return;
	}
	public function __destruct() {
		if ($this->silence) {
			return;
		}
		$fn = date('Y-m-d H-i-s') . ' ' . md5(microtime()) . '.log';
		$f = fopen($this->dir . "/$fn", 'w');
		$time = microtime() - $this->micro;
		$this->log[] = "Execution time: $time";
		$text = '';
		foreach ($this->log as $msg) {
			$text .= $msg . "\n";
		}
		fwrite($f, $text);
		fclose($f);
		return;
	}
	public function log($msg = null) {
		if ($msg === null) {
			return $this->log;
		}
		$this->log[] = $msg;
		return;
	}
	public function mute() {
		$this->silence = true;
		return;
	}
}
?>