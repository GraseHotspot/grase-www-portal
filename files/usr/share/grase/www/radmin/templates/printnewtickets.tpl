<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{$Title} - Print Tickets</title>
<meta name="generator" content="{$Application} {$application_version}" />
<!-- CSS Stylesheet -->
<link rel="stylesheet" type="text/css" href="/grase/hotspot.css" id="hotspot_css" media="screen, projection" />
<link rel="stylesheet" type="text/css" href="/grase/radmin/radmin.css?{$css_version}" id="radmin_css" media="screen, projection" />

<link rel="stylesheet" href="/grase/radmin/css/blueprint/screen.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="/grase/radmin/css/blueprint/print.css" type="text/css" media="print" />
<!--[if lt IE 8]>
  <link rel="stylesheet" href="/grase/radmin/css/blueprint/ie.css" type="text/css" media="screen, projection" />
<![endif]-->

<link rel="stylesheet" href="/grase/radmin/css/tickets_print.css" type="text/css" media="print" />

<script language="Javascript1.2">
{literal}
  <!--
  function printpage() {
  window.print();
  }
  //-->
{/literal}  
</script>

</head>

<body onload="printpage()">

<div class="container">

	<div id="cutout_tickets span-24">

		{foreach from=$users_groups item=group name=grouploop key=id}
		{foreach from=$group|@sortby:"-Group,-#AcctTotalOctets,Username" item=user key=id name=usersloop}
			<div class="cutout_ticket span-6 {if $smarty.foreach.usersloop.iteration % 4 == 0}last{/if}">
				<span class="ticket_item_label span-3">Username</span><span class='info_username span-3 last'>{$user.Username}</span><br/>
				<span class="ticket_item_label span-3">Password</span><span class='info_password span-3 last'>{$user.Password}</span><br/>

				<span class="ticket_item_label span-3">Datalimit</span><span class='info_datalimit span-3 last '>{$user.MaxOctets|bytes}</span><br/>
				<span class="ticket_item_label span-3">Timelimit</span><span class='info_timelimit span-3 last '>{if $user.MaxAllSession>0}{$user.MaxAllSession|seconds}{/if}</span><br/>
				<span class="ticket_item_label span-3">Expiry</span><span class='info_expiry span-3 last'>{$user.FormatExpiration}</span><br/>

			</div>
		{/foreach}
		{/foreach}

	</div>

	{* <div id='userslist' class="span-24" >
		<table border="0" id='userslistTable'>
			<thead>
			<tr id='userattributesRow'>
				<td>Username</td>
				<td>Password</td>
				<td>Data Limit</td>
				<td>Time Limit</td>
				<td>Account Expiry</td>
				<td>Comment</td>
			</tr>
			</thead>
			{foreach from=$users_groups item=group name=grouploop key=id}
			<tr><td id='{$id}_header' colspan='9' class='groupheader' onclick='switchMenu("{$id}")'>{$id}</td></tr>
			<tbody id='{$id}_body'>	

			{foreach from=$group|@sortby:"-Group,-#AcctTotalOctets,Username" item=user key=id name=usersloop}
			<tr id="user_{$user.Username}_Row" class="userrow {if $smarty.foreach.usersloop.iteration is even}even{else}odd{/if} {$user.account_status}" >
				<td class='info_username'><a href="edituser?username={$user.Username}">{$user.Username}</a></td>
				<td class='info_password'>{$user.Password}</td>

				<td class='info_datalimit'>{$user.MaxOctets|bytes}</td>
				<td class='info_timelimit'>{if $user.MaxAllSession>0}{$user.MaxAllSession|seconds}{/if}</td>			
				<td class='info_expiry'>{$user.FormatExpiration}</td>
				<td class='info_comment'>{$user.Comment}</td>			
			</tr>
			{/foreach}
			</tbody>
			{/foreach}

		</table>
	</div> *}

	<div id="generated" class="span-24">
{php}
   global $pagestarttime;
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = round(($endtime - $pagestarttime), 2);
   echo "Page generated in ".$totaltime." seconds on ";    
{/php}{$RealHostname} using
{php}echo \Grase\Util::formatBytes(memory_get_peak_usage(true)) ;{/php} mem
</div>

</div>

</body>
</html>
