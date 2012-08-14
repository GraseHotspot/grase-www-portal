<span class="collapseheader">{t}Group Properties{/t}</span>
<table style="font-size: 80%">
    <tr>
        <th>{t}Name{/t}</th>
        <th>{t}Expiry{/t}</th>
        <th>{t}Max Data (Mb){/t}</th>
        <th>{t}Max Time (mins){/t}</th>
        <th>{t}Recur data limit{/t}</th>
        <th>{t}Recur time limit{/t}</th>
        <th>{t}BW Limit Down{/t}</th>
        <th>{t}BW Limit Up{/t}</th>
    </tr>

{foreach from=$groupsettings item=attributes key=groupname}

    <tr>
        <td>{$attributes.GroupLabel}</td>
        <td>{$attributes.Expiry}</td>
        <td>{$attributes.MaxMb}</td>
        <td>{$attributes.MaxTime}</td>        
        <td>{if $groupdata.$groupname.DataRecurLimit}{assign var=lim value=$groupdata.$groupname.DataRecurLimit}{$Datavals.$lim} per {$groupdata.$groupname.DataRecurTime} {/if}</td>
        <td>{if $groupdata.$groupname.TimeRecurLimit}{assign var=lim value=$groupdata.$groupname.TimeRecurLimit}{$Timevals.$lim} per {$groupdata.$groupname.TimeRecurTime}{/if}</td>
        <td>{if $groupdata.$groupname.BandwidthDownLimit}{assign var=lim value=$groupdata.$groupname.BandwidthDownLimit}{$Bandwidthvals.$lim}{/if}</td>
        <td>{if $groupdata.$groupname.BandwidthUpLimit}{assign var=lim value=$groupdata.$groupname.BandwidthUpLimit}{$Bandwidthvals.$lim}{/if}</td>        

    </tr>
{/foreach}
</table>
