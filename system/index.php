<?php
require_once '../lib/Core.php';
Core::$doc->js('system.js');
Core::$doc->content
		->append('input')
		->set('type', 'button')
		->set('value', 'Очистить кеш')
		->set('id', 'CacheRemove');
?>