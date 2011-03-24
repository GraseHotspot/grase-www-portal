{include file="header.tpl" Name="Create New Machine Account" activepage="createuser"}

<div id="newMachineForm">
<h2>Create New Machine Account</h2>
<div class="errorPage" style="display: {if $error}block;{else}none;{/if}">Error in data, please correct and try again<br/><span id="errorMessage">{foreach from=$error item=msg}{$msg}<br/>{/foreach}</span> </div>

<form method='post' name='newmachine' action=''>


<table>
<tr><td>MAC Address<a class="helpbutton" title='The MAC address is the network hardware address of the computer. It needs to be of the format XX-XX-XX-XX-XX-XX where XX is a hex number, typed in all capitals'><img src="/grase/images/icons/help.png" alt=""/></a></td><td><input type="text" name="mac" value='{$machine.mac}'/></td></tr>
<!-- Password is automatic for machine accounts
<tr><td>Password</td><td> <input type="text" name="Password" value='{$user.Password}'/></td></tr>-->

<!-- Group is automatic for machine accounts
<tr><td>Group</td><td> {html_options name="Group" options=$Usergroups selected=$user.Group}</td></tr>
-->
<tr><td>Comment</td><td> <input type="text" name="Comment" value='{$machine.Comment}'/></td></tr>

<tr><td colspan='2'><a class="helpbutton" title='Machine accounts are intended to be used for computers that need internet access at all times. For example, an office computer. Machine accounts login automatically as soon as they access the network, this will normally occur at startup. If a limit is set, when the limit is reached the computer will revert to the normal login screen for internet access'><img src="/grase/images/icons/help.png" alt=""/> It is not recommended setting a time or data limit for machine accounts.</a></td></tr>

<tr><td colspan='2'>When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left)</td></tr>
<tr><td>Data Limit (Mb)</td><td>{html_options name="Max_Mb" options=$Datacosts selected=$machine.Max_Mb} <span class="form_or">OR</span> <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$machine.MaxMb}' title="Type your own Mb Limit"/>
</td></tr>
<tr><td>Time Limit (Minutes)</td><td>{html_options name="Max_Time" options=$Timecosts selected=$machine.Max_Time} <span class="form_or">OR</span> <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$machine.MaxTime}' title="Type your own Time Limit"/></td></tr>
<!-- Expiry isn't relevant for machine accounts
<tr><td>Expiry (Automatic)<a class="helpbutton" title='Expiry is based on the Group.<br\/>1 Month for visitors<br\/>3 Months for students<br\/>6 Months for staff and ministry'><img src="/grase/images/icons/help.png" alt=""/></a></td><td> {html_select_date disabled='disabled' prefix="Expirydate_" time=$user.Expiration end_year="+1" year_empty='' month_empty='' day_empty=''} </td></tr>
-->


<tr><td></td><td class="buttons"><button class="positive" type="submit" name="newmachinesubmit" value="Create New Machine Account"><img src="/grase/images/icons/tick.png" alt=""/>Create New Machine Account</button></td></tr>
</table>
</form>
</div>

{include file="footer.tpl"}
