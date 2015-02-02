<script>
$(function(){
	$.contextMenu({
		selector: ".currency",
		items: {
			remove: {name: "Удалить", icon: "delete"},
		},
		callback: function(key, options){
			switch (key) {
				case "remove":
					if (!confirm("Вы уверены?"))
						return;
					var currencyID = $(this).attr("id");
					$.post("/processing/currency/delete/", {id: currencyID}, function(answer){
						answer = JSON.parse(answer);
						if (answer.result == 'success')
							$('#' + currencyID).hide("fast", function(){ $(this).remove(); });
						else
							MSG.error(answer.error);
					});
					break;
			}
		},
	});
});
</script>
<table class = "data">
	<tr>
		<th>Номер</th>
		<th>Код</th>
		<th>Название</th>
		<th>Символ</th>
		<th>1/100</th>
	</tr>
	<?php foreach ($args[0] as $Curr):?>
		<tr class = "currency" id = "<?=$Curr->id?>">
			<td><?=$Curr->number?></td>
			<td><?=$Curr->code?></td>
			<td><a href = "./card/?id=<?=$Curr->id?>"><?=$Curr->name?></a></td>
			<td><?=$Curr->unit?></td>
			<td><?=$Curr->coin?></td>
		</tr>
	<?php endforeach;?>
</table>