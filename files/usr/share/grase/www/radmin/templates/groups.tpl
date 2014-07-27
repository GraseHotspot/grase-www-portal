{include file="header.tpl" Name="Groups" activepage="groups"}

<h2>{t}Group Config{/t}</h2>
<p>{t escape=no}Group expiry needs to be in a format understood by the <a target="_blank" href="http://www.php.net/manual/en/function.strtotime.php">strtotime</a> PHP function.{/t}<br/>{t}For example, "+1 month" will set an expiry for 1 month from when the account is created. "+2 weeks", "+3 days" etc.{/t} {t}Expiry is calculated to the second, so if you want it to a date for example, try "+1 week midnight".{/t}</p>

<p>{t escape=no}Login Time must be in the format of UUCP time ranges. See <a target="_blank" href="http://ftp.gnu.org/pub/old-gnu/Manuals/radius/html_node/radius_186.html">http://ftp.gnu.org/pub/old-gnu/Manuals/radius/html_node/radius_186.html</a> for more details. Only basic format checking is done, so please ensure that the correct format is used. Leaving Login Times blank will allow login at any time.{/t}
</p>

<p>{t}Deleting a group won't delete it's users. Next time the user is edited it's group will become the default group unless a new group is selected.{/t}</p>

<p>{t}The limits here are the default for group members, unless overridden when creating a member. The limits are applied at user creation time, if "Inherit from group" is selected. If multiple limits are specified, the first limit to be reached will disconnect the user.{/t}</p>

<p>{t}Changing Expiry, Data or Time limits, will not change existing users of the group and will only apply to new users. Recurring limits, Bandwidth and simultaneous logins will all apply to existing and new members.{/t}</p>

<p class="ui-widget messagewidget error">{t}Recurring Data and Time limits must not be used with simultaneous login set to yes, otherwise users may be able to use more than the allocated limit. Data limits should only be used with larger time periods, and users may need to logout and log back in if they use past the time period.{/t}</p>
<div id="GroupConfigForm">
<form method="post" action="?" class="generalForm">




    <div>
      <h3>{t}Groups{/t}</h3>
      
      <button id="addgroup">New Group</button>
        
      <div id='groupslist' style="overflow:hidden;">
        <ul id="tabselector">
                 
        </ul>
      

        
    {foreach from=$groupsettings item=settings key=groupname name=groupsettingsloop}        
        <div class="jsmultioption" id="groupSettings_{$smarty.foreach.groupsettingsloop.iteration}" class="tabcontent">
            <label>{t}Name{/t}</label><input type="text" class="groupnameinput" name="groupname[]" value='{$settings.GroupLabel}'/>
            <label>{t}Description{/t}</label><textarea name="groupcomment[]" class="groupcommentinput" maxlength='250'>{$settings.Comment}</textarea>
            <label>{t}Expiry{/t}</label><input type="text" placeholder="{t}Never Expire{/t}" name="groupexpiry[]" value='{$settings.Expiry}'/>
            
            <label>{t}Login Times{/t}</label><input type="text" name="LoginTime[]" value='{$groupcurrentdata.$groupname.LoginTime}'/>            
            
            <label>{t}Default Data Limit (MiB){/t}</label>
            {html_options name="Group_Max_Mb[]" options=$GroupDatacosts selected=$settings.MaxMb}
            
            <label>{t}Default Time Limit (Minutes){/t}</label>
            {html_options name="Group_Max_Time[]" options=$GroupTimecosts selected=$settings.MaxTime}
            
            <label>{t}Recurring Data Limit (MiB){/t}</label>
            {html_options name="Recur_Data_Limit[]" options=$Datavals selected=$groupcurrentdata.$groupname.DataRecurLimit}{t}per{/t}
            {html_options name="Recur_Data[]" options=$Recurtimes selected=$groupcurrentdata.$groupname.DataRecurTime}
            
            <label>{t}Recurring Time Limit (Minutes){/t}</label>
            {html_options name="Recur_Time_Limit[]" options=$Timevals selected=$groupcurrentdata.$groupname.TimeRecurLimit}{t}per{/t}
            {html_options name="Recur_Time[]" options=$Recurtimes selected=$groupcurrentdata.$groupname.TimeRecurTime}
            
            <label>{t}Bandwidth Limit Down{/t}</label>
            {html_options name="Bandwidth_Down_Limit[]" options=$Bandwidthvals selected=$groupcurrentdata.$groupname.BandwidthDownLimit}           
            
            <label>{t}Bandwidth Limit Up{/t}</label>
            {html_options name="Bandwidth_Up_Limit[]" options=$Bandwidthvals selected=$groupcurrentdata.$groupname.BandwidthUpLimit}
            
            <label>{t}Number of simultaneous logins. Leave Blank for unlimited{/t}</label>
            <input type="number" min="1" placeholder="{t}Unlimited{/t}" name="SimultaneousUse[]" value="{$groupcurrentdata.$groupname.SimultaneousUse}"/>
            
            
            
            <!--<div class="jsremove ui-widget-content">
                <span class="jsremovebutton"></span>
                <span class="jsaddremovetext">{t}Delete Group{/t}</span>
            </div> -->
            <hr/>           
        </div>

    {/foreach}
        <div class="jsmultioption" id="groupSettingsNewGroup" class="tabcontent">
            <label>{t}Name{/t}</label><input type="text" name="groupname[]" class="groupnameinput" value=''/>
            <label>{t}Description{/t}</label><textarea name="groupcomment[]" class="groupcommentinput" maxlength='250'></textarea>
            <label>{t}Expiry{/t}</label><input type="text" placeholder="{t}Never Expire{/t}" name="groupexpiry[]" value=''/>
            <label>{t}Login Times{/t}</label><input type="text" name="LoginTime[]" value=''/>  
            <label>{t}Default Data Limit (MiB){/t}</label>
            {html_options name="Group_Max_Mb[]" options=$GroupDatacosts}
            <label>{t}Default Time Limit (Minutes){/t}</label>
            {html_options name="Group_Max_Time[]" options=$GroupTimecosts}

            <label>{t}Recurring Data Limit (MiB){/t}</label>
            {html_options name="Recur_Data_Limit[]" options=$Datavals}{t}per{/t}
            {html_options name="Recur_Data[]" options=$Recurtimes}            
            
            <label>{t}Recurring Time Limit (Minutes){/t}</label>
            {html_options name="Recur_Time_Limit[]" options=$Timevals }{t}per{/t}
            {html_options name="Recur_Time[]" options=$Recurtimes}
            
            <label>{t}Bandwidth Limit Down{/t}</label>
            {html_options name="Bandwidth_Down_Limit[]" options=$Bandwidthvals}           
            
            <label>{t}Bandwidth Limit Up{/t}</label>
            {html_options name="Bandwidth_Up_Limit[]" options=$Bandwidthvals}
            
            <label>{t}Number of simultaneous logins. Leave Blank for unlimited{/t}</label>
            <input type="number" min="1"  placeholder="{t}Unlimited{/t}" nname="SimultaneousUse[]" value=""/>
            
        </div>
        <span id="groupsInfo"></span>

      </div>

    </div>

    <button type="submit" name="submit">{t}Save Settings{/t}</button>


</form>

</div>

{include file="footer.tpl"}
