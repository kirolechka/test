<script>
$(document).ready(function(){
	$('#CurrencyEditForm').ajaxForm(function(data){
		data = JSON.parse(data);
		if (data.result == 'success')
			MSG.success();
		else
			MSG.error('Ошибка', data.error.msg);
	});
});
</script>
<form id = "CurrencyEditForm" action = "/processing/currency/edit/" method = "post">
	<table class = "data">
		<tr>
			<td>Номер</td>
			<td><input type = "text" name = "number" value = "<?=$args[0]->number?>"></td>
		</tr>
		<tr>
			<td>Код</td>
			<td><input type = "text" name = "code" value = "<?=$args[0]->code?>"></td>
		</tr>
		<tr>
			<td>Название</td>
			<td><input type = "text" name = "name" value = "<?=$args[0]->name?>"></td>
		</tr>
		<tr>
			<td>Символ</td>
			<td><input type = "text" name = "unit" value = "<?=$args[0]->unit?>"></td>
		</tr>
		<tr>
			<td>1/100</td>
			<td><input type = "text" name = "coin" value = "<?=$args[0]->coin?>"></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type = "hidden" name = "id" value = "<?=$args[0]->id?>">
				<input type = "submit" value = "Сохранить">
			</td>
		</tr>
	</table>
</form>