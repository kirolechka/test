<?php
function get_client_ip() {
	$ipaddress = '';
	if (@$_SERVER['HTTP_CLIENT_IP'])
		return $_SERVER['HTTP_CLIENT_IP'];
	if (@$_SERVER['HTTP_X_FORWARDED_FOR'])
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	if (@$_SERVER['HTTP_X_FORWARDED'])
		return $_SERVER['HTTP_X_FORWARDED'];
	if (@$_SERVER['HTTP_FORWARDED_FOR'])
		return $_SERVER['HTTP_FORWARDED_FOR'];
	if (@$_SERVER['HTTP_FORWARDED'])
		return $_SERVER['HTTP_FORWARDED'];
	if (@$_SERVER['REMOTE_ADDR'])
		return $_SERVER['REMOTE_ADDR'];
	return 'UNKNOWN';
}

function array_mix (array $data) {
	$res = [];
	foreach ($data as $k => $v)
		foreach ($v as $kk => $vv)
			$res[$kk][$k] = $vv;
	return $res;
}
?>