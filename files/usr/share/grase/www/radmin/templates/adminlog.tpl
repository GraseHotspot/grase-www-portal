{include file="header.tpl" Name="Admin Log" activepage="adminlog"}

<h2>Admin Log</h2>

<div id='log' style='display:block;'>
	<table border="0" id='AdminlogTable' class="stripeMe">
	    <thead>
		<tr>
			<td>Timestamp</td>
			<td>Username</td>
			<td>IP</td>
			<td>Action</td>
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
