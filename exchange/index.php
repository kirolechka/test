<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lib/Core.php";
$Crumbs = new ProtoHTMLBreadcrumbs();
$Crumbs->crumb("Курсы валют");
Core::$doc->content->append($Crumbs);
Core::$doc->content->template("buttons.php");
$Exchanges = Exchange::select();
Core::$doc->content->template("table.php", $Exchanges);
?>