{include file="header.tpl" Name="Admin Log" activepage="adminlog"}

<h2>{t}Admin Log{/t}</h2>

<div id='log' style='display:block;'>
	<table border="0" id='AdminlogTable' class="stripeMe">
	    <col style="width: 10em"/>
	    <col style="width: 6em"/>
	    <col style="width: 6em"/>	    	    
	    <col />
	    <thead>
		<tr>
			<td>{t}Timestamp{/t}</td>
			<td>{t}Username{/t}</td>
			<td>{t}IP{/t}</td>
			<td>{t}Action{/t}</td>
		</tr>
		</thead>
		<tbody>
		{foreach from=$loglines item=logline}
		<tr>
			<td>{$logline.timestamp}</td>
			<td>{$logline.username}</td>
			<td>{$logline.ipaddress}</td>
			<td>{$logline.action}</td>															
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>

{include file="footer.tpl"}
