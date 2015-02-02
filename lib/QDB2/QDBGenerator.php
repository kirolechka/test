<?php
class QDBGenerator {
	public static function insert(QDBConfig $config, $table, array $data) {
		$keys = static::__keys($data);
		$vals = static::__vals($config, $data);
		$data = static::__metamorph($data);
		return "insert into `$table` ($keys) values ($vals)";
	}
	public static function update(QDBConfig $config, $table, array $data) {
		$set = static::set($config, $data);
		$data = static::__metamorph($data);
		return "update `$table` set $set";
	}
	private static function __metamorph(array $data) {
		$res = array();
		foreach ($data as $k => $v) {
			if ($k[0] == ':') {
				$res[$k] = $v;
			}
			else {
				$res[":$k"] = $v;
			}
		}
		return $res;
	}
	private static function __keys(array $data) {
		$res = "";
		foreach ($data as $k => $v) {
			if ($k[0] != ':' && $res) {
				$res .= ", ";
			}
			if ($k[0] != ':') {
				$res .= "`$k`";
			}
		}
		return $res;
	}
	private static function __vals(QDBConfig $config, array $data) {
		$res = "";
		foreach ($data as $k => $v) {
			if ($k[0] != ':' && $res) {
				$res .= ", ";
			}
			if ($k[0] != ':') {
				$fieldConfig = $config->$k;
                $res .= ":$k";
			}
		}
		return $res;
	}
	private static function set(QDBConfig $config, array $data) {
		$res = "";
		foreach ($data as $k => $v) {
			if ($k[0] != ':' && $res) {
				$res .= ", ";
			}
			if ($k[0] != ':') {
				$fieldConfig = $config->$k;
                $res .= "`$k` = :$k";
			}
		}
		return $res;
	}
}
?>