<div id="{$useraction}Form">
<h2>{$useractionTitle}</h2>
<div class="errorPage" style="display: {if $error}block;{else}none;{/if}">Error in data, please correct and try again<br/><span id="errorMessage">{foreach from=$error item=msg}{$msg}<br/>{/foreach}</span> </div>

<!-- Not using this method, using OR method
<ul class="timecost_list">
{foreach from=$Timecosts key=time item=label}
<li><a title="{$time}" class="timecost_item">{$label}</a></li>
{/foreach}
</ul>

<ul class="datacost_list">
{foreach from=$Datacosts key=mb item=label}
<li><a title="{$mb}" class="datacost_item">{$label}</a></li>
{/foreach}
</ul>

-->

<form method='post' name='newuser' action=''>


<table>
<tr><td>Username</td><td><input {if $usernamelock}disabled='disabled'{/if} type="text" name="Username" value='{$user.Username}'/></td></tr>
<tr><td>Password</td><td> <input type="text" name="Password" value='{$user.Password}' onkeyup="runPassword(this.value, 'newpassword');" />
	                        <div style="width: 200px;float: right;"> 
                                <div id="newpassword_text" ></div>
                                <div id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></div> 	
</td></tr>

<tr><td>Group</td><td> {html_options name="Group" options=$Usergroups selected=$user.Group}</td></tr>
<!--<tr><td>Group</td><td> <input type="select" name="group" value='{$user.group}'></td></tr>-->
<tr><td>Comment</td><td> <input type="text" name="Comment" value='{$user.Comment}'/></td></tr>


<tr><td colspan='2'>When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left)</td></tr>
<tr><td>Data Limit (Mb)</td><td>{html_options name="Max_Mb" options=$Datacosts selected=$user.Max_Mb} <span class="form_or">OR</span> <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb}' title="Type your own Mb Limit"/>
</td></tr>
<tr><td>Time Limit (Minutes)</td><td>{html_options name="Max_Time" options=$Timecosts selected=$user.Max_Time} <span class="form_or">OR</span> <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="Type your own Time Limit"/></td></tr>
<tr><td>Expiry (Automatic)<a class="helpbutton" onclick="ShowContent('helpbox','Expiry is based on the Group.<br\/>1 Month for visitors<br\/>3 Months for students<br\/>6 Months for staff and ministry');" ><img src="/grase/images/icons/help.png" alt=""/></a></td><td> {html_select_date disabled='disabled' prefix="Expirydate_" time=$user.Expiration end_year="+1" year_empty='' month_empty='' day_empty=''} </td></tr>


<tr><td></td><td class="buttons"><button class="positive" type="submit" name="{$useraction}submit" value="{$useractionlabel}"><img src="/grase/images/icons/tick.png" alt=""/>{$useractionlabel}</button></td></tr>
</table>
</form>
</div>
