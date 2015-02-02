<?php
if(@$_GET['lang'] == 'en') $_SESSION['lang'] = 'en';
if(@$_GET['lang'] == 'ru') $_SESSION['lang'] = 'ru';

if (($_SESSION['lang']) == 'en')
	$translate = new Translator($_SESSION['lang']);
else
	$translate = new Translator('ru');

$langi = @$_SESSION['lang']<>'ru' ? 'ru' : 'en';

$menu = new ProtoHTMLMenu('ul', 'menu');
$menu->separator();
$menu->separator();
$currency = $module->submenu('Валюта', '/currencyList/');
$currency->item('Курс валют', '/exchange/');

Core::$doc->title('test');
//menu left
Core::$doc->head->text('
  <script>
  $(function() {
    $( "#menu_left" ).menu();
 });
  </script>
  <style>
  .ui-menu { width: 150px; }
  </style>
');
Core::$doc->body->text('<section id="wrapper">
						<header>
		<div class="navbar navbar-fixed-top">
			<div class="separator"></div>
				<div class="container" style="width: 990px !important"><div class="separator">
			');
Core::$doc->body->append($menu);
Core::$doc->body->text('	
				</div>
				</div>
	</div>
		</header>
			<section id="content">
				<div class="container">
			   	<div class="sub_wrapper">
					<div class="centered">');
?>
