{include file="header.tpl" Name="" activepage="nojsstatus"}

{if $error}
			<div class="ui-widget" id="errormessages">
				<div class="ui-state-error ui-corner-all"  style="margin-top: 20px; padding: 0pt 0.7em;" > 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
					<ul>{foreach from=$error item=msg}<li><strong>{$msg}</strong></li>{/foreach}</ul></p>

				</div>
			</div>
{/if}

<div id="statusPage">

<p id="loggedinusername">You are logged in as: <span id="UserNameLink">{$user.Username}</span></p>
<p id="myaccountlink"><a href="/grase/radmin/usermin" onclick="window.open(this.href,'_blank');return false;">Access My Account</a></p>

<table border="0" id="statusTable" style="padding-top:4px;font-size:70%">
<tr id="UserNameRow" class="chilliLabelhide">
<td id="UserNameLabel" class="chilliLabel"><b>User</b></td>
<td id="UserNameLinkCell" class="chilliValue"><span id="UserNameLink1">{$user.Username}</span></td>
</tr>
<tr id="sessionUsageRow">
<td id="sessionUsageLabel" class="chilliLabel"><b>Usage this session</b></td>
<td id="sessionUsage" class="chilliValue">{$session.AcctTotalOctets|bytes}</td>
</tr>
<tr id="maxRemainOctetsRow">
<td id="maxRemainOctetsLabel" class="chilliLabel"><b>Remaining Quota</b></td>
<td id="maxRemainOctets" class="chilliValue">{$user.RemainingQuota|bytes}</td>
</tr>
<tr id="download_bar_row">
<td></td><td id="download_bar_cell"><span id="download_bar" style="display:none;"> </span></td>
</tr>
<tr id="MonthlyUsageLimitRow">
<td id="MonthlyUsageLimitRowLabel" class="chilliLabel"><b>Quota allocation</b></td>
<td id="MonthlyUsageLimit" class="chilliValue">{$user.MaxOctets|bytes}</td>
</tr>
<tr id="sessionTimeoutRow" class="chilliLabelhide">
<td id="sessionTimeoutLabel" class="chilliLabel"><b>Max Session Time</b></td>
<td id="sessionTimeout" class="chilliValue">Not available</td>
</tr>
<tr id="idleTimeoutRow" class="chilliLabelhide">
<td id="idleTimeoutLabel" class="chilliLabel"><b>Max Idle Time</b></td>
<td id="idleTimeout" class="chilliValue">Not available</td>
</tr>
<tr id="startTimeRow" class="chilliLabel">
<td id="startTimeLabel" class="chilliLabel"><b>Start Time</b></td>
<td id="startTime" class="chilliValue">{$session.AcctStartTime}</td>
</tr>
<tr id="sessionTimeRow">
<td id="sessionTimeLabel" class="chilliLabel"><b>Session Time</b></td>
<td id="sessionTime" class="chilliValue">{$session.AcctSessionTime|seconds}</td>
</tr>
<tr id="RemainsessionTimeRow">
<td id="RemainsessionTimeLabel" class="chilliLabel"><b>Remaining Time</b></td>
<td id="RemainsessionTime" class="chilliValue">{$user.RemainingTime|seconds}</td>
</tr>
<tr id="MonthlyTimeLimitRow">
<td id="MonthlyTimeLimitLabel" class="chilliLabel"><b>Time Limit</b></td>
<td id="MonthlyTimeLimit" class="chilliValue">{$user.MaxAllSession|seconds}</td>
</tr>
<tr id="idleTimeRow" class="chilliLabelhide">
<td id="idleTimeLabel" class="chilliLabel"><b>Idle Time</b></td>
<td id="idleTime" class="chilliValue">Not available</td>
</tr>
<tr id="inputOctetsRow" class="chilliLabel">
<td id="inputOctetsLabel" class="chilliLabel"><b>Downloaded</b></td>
<td id="inputOctets" class="chilliValue">{$session.AcctInputOctets|bytes}</td>
</tr>
<tr id="outputOctetsRow" class="chilliLabel">
<td id="outputOctetsLabel" class="chilliLabel"><b>Uploaded</b></td>
<td id="outputOctets" class="chilliValue">{$session.AcctOutputOctets|bytes}</td>
</tr>
<tr id="connectRow">
<td><span id="statusMessage"></span></td>
<td class="buttons"><a href="http://10.1.0.1:3990/logoff" id="logoutlink" class="negative"> <img src="/grase/images/icons/cross.png" alt=""/>
Logout</a></td>
</tr>

</table>
</div>

{include file="footer.tpl" hide="true"}
