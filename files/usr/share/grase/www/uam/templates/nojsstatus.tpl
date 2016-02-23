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

<p id="loggedinusername">{t}You are logged in as:{/t} <span id="UserNameLink">{$user.Username}</span></p>
<p id="myaccountlink"><a href="/grase/radmin/usermin" onclick="window.open(this.href,'_blank');return false;">{t}Access My Account{/t}</a></p>

<table border="0" id="statusTable" style="padding-top:4px;font-size:70%">
<tr id="UserNameRow" class="chilliLabelhide">
<td id="UserNameLabel" class="chilliLabel"><b>{t}User{/t}</b></td>
<td id="UserNameLinkCell" class="chilliValue"><span id="UserNameLink1">{$user.Username}</span></td>
</tr>
<tr id="sessionUsageRow">
<td id="sessionUsageLabel" class="chilliLabel"><b>{t}Usage this session{/t}</b></td>
<td id="sessionUsage" class="chilliValue">{$session.AcctTotalOctets|bytes}</td>
</tr>
<tr id="maxRemainOctetsRow">
<td id="maxRemainOctetsLabel" class="chilliLabel"><b>{t}Remaining Quota{/t}</b></td>
<td id="maxRemainOctets" class="chilliValue">{if $user.RemainingQuota gt 0}{$user.RemainingQuota|bytes}{else}{t}Unlimited{/t}{/if}</td>
</tr>
<tr id="download_bar_row">
<td></td><td id="download_bar_cell"><span id="download_bar" style="display:none;"> </span></td>
</tr>
<tr id="MonthlyUsageLimitRow">
<td id="MonthlyUsageLimitRowLabel" class="chilliLabel"><b>{t}Quota allocation{/t}</b></td>
<td id="MonthlyUsageLimit" class="chilliValue">{if $user.MaxOctets gt 0}{$user.MaxOctets|bytes}{else}{t}Unlimited{/t}{/if}</td>
</tr>
<tr id="sessionTimeoutRow" class="chilliLabelhide">
<td id="sessionTimeoutLabel" class="chilliLabel"><b>{t}Max Session Time{/t}</b></td>
<td id="sessionTimeout" class="chilliValue">{t}Not available{/t}</td>
</tr>
<tr id="idleTimeoutRow" class="chilliLabelhide">
<td id="idleTimeoutLabel" class="chilliLabel"><b>{t}Max Idle Time{/t}</b></td>
<td id="idleTimeout" class="chilliValue">{t}Not available{/t}</td>
</tr>
<tr id="startTimeRow" class="chilliLabel">
<td id="startTimeLabel" class="chilliLabel"><b>{t}Start Time{/t}</b></td>
<td id="startTime" class="chilliValue">{$session.AcctStartTime}</td>
</tr>
<tr id="sessionTimeRow">
<td id="sessionTimeLabel" class="chilliLabel"><b>{t}Session Time{/t}</b></td>
<td id="sessionTime" class="chilliValue">{$session.AcctSessionTime|seconds}</td>
</tr>
<tr id="RemainsessionTimeRow">
<td id="RemainsessionTimeLabel" class="chilliLabel"><b>{t}Remaining Time{/t}</b></td>
<td id="RemainsessionTime" class="chilliValue">{if $user.RemainingTime gt 0}{$user.RemainingTime|seconds}{else}{t}Unlimited{/t}{/if}</td>
</tr>
<tr id="MonthlyTimeLimitRow">
<td id="MonthlyTimeLimitLabel" class="chilliLabel"><b>{t}Time Limit{/t}</b></td>
<td id="MonthlyTimeLimit" class="chilliValue">{if $user.MaxAllSession gt 0}{$user.MaxAllSession|seconds}{else}{t}Unlimited{/t}{/if}</td>
</tr>
<tr id="idleTimeRow" class="chilliLabelhide">
<td id="idleTimeLabel" class="chilliLabel"><b>{t}Idle Time{/t}</b></td>
<td id="idleTime" class="chilliValue">{t}Not available{/t}</td>
</tr>
<tr id="inputOctetsRow" class="chilliLabel">
<td id="inputOctetsLabel" class="chilliLabel"><b>{t}Downloaded{/t}</b></td>
<td id="inputOctets" class="chilliValue">{$session.AcctInputOctets|bytes}</td>
</tr>
<tr id="outputOctetsRow" class="chilliLabel">
<td id="outputOctetsLabel" class="chilliLabel"><b>{t}Uploaded{/t}</b></td>
<td id="outputOctets" class="chilliValue">{$session.AcctOutputOctets|bytes}</td>
</tr>
<tr id="connectRow">
<td><span id="statusMessage"></span></td>
<td class="buttons"><a href="http://1.0.0.0/" id="logoutlink" class="negative"> <img src="/grase/images/icons/cross.png" alt=""/>
Logout</a></td>
</tr>

</table>
</div>

{include file="footer.tpl" hide="true"}
