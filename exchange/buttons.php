<script>
$(function(){
	$("#CurrencyListUpdateButton").click(function(){
		if (post("/processing/exchange/update/"))
			location.reload();
	});
});
</script>
<button id = "CurrencyListUpdateButton">Обновить сейчас</button>