<?php
require_once '../Core.php';
ERRH::$logFile = false;
ERRH::$logHTML = false;
ERRH::$logJS = false;
ERRH::$logText = true;
Core::$dom->echoOnDie = false;
echo "CORE Generate Template\n";
$opt = getopt('', array('type:', 'filename:', 'module:'));
if (!isset($opt['type']) || !isset($opt['filename']) || !isset($opt['module'])) {
	echo "Error\n";
	die;
}
$module = Core::getModule($opt['module']);
switch ($opt['type']) {
	case 'table': $module->generateTemplateTable($opt['filename']); break;
	case 'form' : $module->generateTemplateForm($opt['filename']); break;
	default: echo "Unknown Type\n"; break;
}
echo "Finish\n";
?>