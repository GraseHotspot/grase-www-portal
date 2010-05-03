{include file="header.tpl" Name="Monthly Accounts" activepage="monthly_accounts"}

<div id='yearlist'>
{foreach from=$monthly_accounts item=month name=monthsloop key=date}
	<div id='{$date}_accounts'>
	<h3>{$date}</h3>
		<table border="0" id='{$date}_data_table'>
			<thead>
			<tr id='{$date}_header' class="monthheader" onclick='switchMenu("{$date}")'>
				<td class="switcher">Username</td>
				<td>Downloaded</td>
				<td>Uploaded</td>
				<td>Total Data Usage</td>
			</tr>	
			</thead>
			<tbody id='{$date}_body'>
			{foreach from=$month item=user_data name=usersloop}
			<tr id='session_{$session.id}_Row' class="month_user_row {if $smarty.foreach.usersloop.iteration is even}even{else}odd{/if}">
				<td>{$user_data.UserName}</td>
				<td class="numbers">{$user_data.InputOctets|bytes}</td>
				<td class="numbers">{$user_data.OutputOctets|bytes}</td>			
				<td class="numbers">{$user_data.TotalOctets|bytes}</td>			
			</tr>
			{/foreach}
			</tbody>
			<tbody id='{$date}_totals'>
			<tr id='session_{$session.id}_Row' class="totalrow">
				<td>Total</td>
				<td class="numbers">{$monthly_accounts_totals.$date.TotalInputOctets|bytes}</td>
				<td class="numbers">{$monthly_accounts_totals.$date.TotalOutputOctets|bytes}</td>			
				<td class="numbers">{$monthly_accounts_totals.$date.TotalOctets|bytes}</td>			
			</tr>
			</tbody>
		</table>
	</div>
{/foreach}
</div>

{include file="footer.tpl"}
