<div id="{$useraction}Form">
<h2>{t}{$useractionTitle}{/t}</h2>

<form method='post' id='newuserform' action='?' class='generalForm'>

<div>
    <label for='Username'>{t}Username{/t}</label>
    <input {if $usernamelock}disabled='disabled'{/if} type="text" id="Username" name="Username" value='{$user.Username}' required="required"/>
    <span id="UsernameInfo">{t}Choose a username{/t}</span>
</div>
<div>
    <label for='Password'>{t}Password{/t}</label>
    <input type="text" name="Password" id="Password" value='{$user.Password}' onkeyup="runPassword(this.value, 'newpassword');" required="required"/>
    <span id='PasswordInfo'>{t}Choose a secure password for the user{/t}</span>
                                <span id="newpassword_text" ></span>
                                <span id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></span> 
    
</div>
<div>
    <label for='Group'>{t}Group{/t}</label>
    {html_options name="Group" id="Group" options=$groups selected=$user.Group}    
    <span id='GroupInfo'>{t}Choose the users group (Expiry is based on the user group){/t}</span>
    <br/>{include file="grouppropertiesinfo.tpl"}
</div>
<div>
    <label for='Comment'>{t}Comment{/t}</label>
    <input type="text" name="Comment" id="Comment" value='{$user.Comment|escape}'/>
    <span id='CommentInfo'>{t}A comment about the user{/t}</span>
</div>

    <p><span>{t}When ether limit is reached, the user will be cut off. (i.e. after 1hour even if they still have data left){/t}<br/>
    {t}A limit of 0 does not mean unlimited, it will immediately lock the user out. To have an unlimited user, the user must be created without any limits.{/t}<br/><strong>{t}If a limit is not set here, but is defined for the group, then the group limit will apply{/t}</strong></span></p>

<div>
    <label for='MaxMb'>{t}Data Limit (MiB){/t}</label>
    {html_options name="Max_Mb" options=$Datacosts selected=$user.Max_Mb}
    <span class="form_or">{t}OR{/t}</span>
    <input type="text" class="default_swap" id="MaxMb" name="MaxMb" value='{$user.MaxMb|displayLocales}' placeholder="{t}Type your own Mb Limit{/t}"/>
    <span id='Max_MbInfo'>{t}Choose a Data Limit OR Type your own value{/t}</span>
</div>
<div>
    <label for='MaxTime'>{t}Time Limit (Minutes){/t}</label>
    {html_options name="Max_Time" id="Max_Time" options=$Timecosts selected=$user.Max_Time}
    <span class="form_or">{t}OR{/t}</span>
    <input type="text" class="default_swap" id="MaxTime" name="MaxTime" value='{$user.MaxTime|displayLocales}' placeholder="{t}Type your own Time Limit{/t}"/>
    <span id='Max_TimeInfo'>{t}Choose a Time Limit OR Type your own value{/t}</span>
</div>

     <p><button type="submit" name="{$useraction}submit" value="{t}{$useractionlabel}{/t}"><img src="/grase/images/icons/tick.png" alt=""/>{t}{$useractionlabel}{/t}</button></p>
</form>
</div>
