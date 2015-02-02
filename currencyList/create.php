<script>
$(document).ready(function(){
	$('#CurrencyCreateForm').ajaxForm(function(data){
		data = JSON.parse(data);
		if (data.result == 'success')
			location = '/currencyList/card/?id=' + data.currency;
		else
			MSG.error('Ошибка', data.error.msg);
	});
});
</script>
<form id = "CurrencyCreateForm" action = "/processing/currency/create/" method = "post">
	<table class = "data">
		<tr>
			<td>Номер</td>
			<td><input type = "text" name = "number"></td>
		</tr>
		<tr>
			<td>Код</td>
			<td><input type = "text" name = "code"></td>
		</tr>
		<tr>
			<td>Название</td>
			<td><input type = "text" name = "name"></td>
		</tr>
		<tr>
			<td>Символ</td>
			<td><input type = "text" name = "unit"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type = "hidden" name = "type" value = "method"><input type = "submit" value = "Создать"></td>
		</tr>
	</table>
</form>
