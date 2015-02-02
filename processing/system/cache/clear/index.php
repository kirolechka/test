<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Core.php';
$p = new Processing();
QCache::clear();
$p->success();
?>