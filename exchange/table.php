<p class = "notice">Внимание курсы валют указаны с округлением до 2-х знаков после точки.</p>
<table class="data">
    <thead>
	    <tr><th colspan = 7 style = "text-align: center;">Курсы по ЦБРФ</th></tr>
    </thead>
    <tbody>
    	<?php foreach ($args[0] as $Exchange): ?>
    	<tr>
    		<td>
    			<?=$Exchange->getBaseCurrency()
    						->format(1);?>
    		</td>
    		<td>=</td>
    		<td>
    			<?=$Exchange->getTargetCurrency()
    						->format($Exchange->course);?>
    		</td>
    		<td>⇄</td>
    		<td>
    			<?=$Exchange->getTargetCurrency()
    						->format(1);?>
    		</td>
    		<td>=</td>
    		<td>
    			<?=$Exchange->getBaseCurrency()
    						->format(1 / $Exchange->course);?>
    		</td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>
</table>