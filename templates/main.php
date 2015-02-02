<?php
$menu = ProtoHTML::menu('menu');
$menu->separator();
$menu->separator();
$currency = $module->submenu('Валюта', '/currencyList/');
$currency->item('Курс валют', '/exchange/');
?>
<section id="wrapper">
	<header>
		<div id ="navbar" class="navbar navbar-fixed-top">
			<div class="separator"><a href="/"></div>
			<div class="container" style="width: 990px !important">
				<div class="separator"><?=$menu?></div>
			</div>
		</div>
	</header>
	<section id="content">
		<div class="container">
			<div class="sub_wrapper">
				<div class="centered" id="CONTENT"></div>
			</div>
		</div>
	</section>
	<footer>
		<div class="footer">
	       <ul class="inline copyright">
				<li class="pull-left"></li>
				<li class="pull-right"></li>
			</ul>
    	</div>
	</footer>
</section>