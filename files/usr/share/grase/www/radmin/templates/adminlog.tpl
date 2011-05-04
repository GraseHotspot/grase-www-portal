{include file="header.tpl" Name="Admin Log" activepage="adminlog"}

<h2>{t}Admin Log{/t}</h2>

<div id='log' style='display:block;'>
<form id="filter-form">Filter: <input name="filter" id="filter" value="" maxlength="30" size="30" type="text"></form><br>
	<table border="0" id='AdminlogTable' class="stripeMe">
	    <col style="width: 10em"/>
	    <col style="width: 9em"/>
	    <col style="width: 6em"/>	    	    
	    <col />
	    <thead>
		<tr>
			<th>{t}Timestamp{/t}</th>
			<th>{t}Username{/t}</th>
			<th>{t}IP{/t}</th>
			<th>{t}Action{/t}</th>
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
