<div id="{$useraction}Form">
<h2>{$useractionTitle}</h2>

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

<form method='post' id='newuserform' action='' class='generalForm'>

<div>
    <label for='Username'>Username</label>
    <input {if $usernamelock}disabled='disabled'{/if} type="text" name="Username" value='{$user.Username}'/>
    <span id="UsernameInfo">Choose a username</span>
</div>
<div>
    <label for='Password'>Password</label>
    <input type="text" name="Password" id="Password" value='{$user.Password}' onkeyup="runPassword(this.value, 'newpassword');" />
    <span id='PasswordInfo'>Choose a secure password for the user</span>
                                <span id="newpassword_text" ></span>
                                <span id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></span> 
    
</div>
<div>
    <label for='Group'>Group</label>
    {html_options name="Group" options=$Usergroups selected=$user.Group}    
    <span id='GroupInfo'>Choose the users group (Expiry is based on the user group)</span>
</div>
<div>
    <label for='Comment'>Comment</label>
    <input type="text" name="Comment" value='{$user.Comment}'/>
    <span id='CommentInfo'>A comment about the user</span>
</div>

    <p><span>When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left)<br/>
    A limit of 0 does not mean unlimited, it will immediately lock the user out. To have an unlimited user, the user must be created without any limits.</span></p>

<div>
    <label for='Max_Mb'>Data Limit (MiB)</label>
    {html_options name="Max_Mb" options=$Datacosts selected=$user.Max_Mb}
    <span class="form_or">OR</span>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb}' title="Type your own Mb Limit"/>
    <span id='Max_MbInfo'>Choose a Data Limit OR Type your own value</span>
</div>
<div>
    <label for='Max_Time'>Time Limit (Minutes)</label>
    {html_options name="Max_Time" options=$Timecosts selected=$user.Max_Time}
    <span class="form_or">OR</span>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime}' title="Type your own Time Limit"/>
    <span id='Max_TimeInfo'>Choose a Time Limit OR Type your own value</span>
</div>
<!--<div>
    <label for='Expiration'>Expiry (Automatic)<a class="helpbutton" title='Expiry is based on the Group.<br\/>1 Month for visitors<br\/>3 Months for students<br\/>6 Months for staff and ministry'><img src="/grase/images/icons/help.png" alt=""/></a></label>
    {html_select_date disabled='disabled' prefix="Expirydate_" time=$user.Expiration end_year="+1" year_empty='' month_empty='' day_empty=''}
    <span id='ExpirationInfo'>The expiry is automatically set based on the Group</span>
</div>-->
     <p><button type="submit" name="{$useraction}submit" value="{$useractionlabel}"><img src="/grase/images/icons/tick.png" alt=""/>{$useractionlabel}</button></p>
</form>
</div>
