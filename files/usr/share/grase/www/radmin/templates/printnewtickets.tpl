<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>{$Title} - {$batchTitle}</title>
    <meta name="generator" content="{$Application} {$application_version}"/>
    <!-- CSS Stylesheet -->
    <style type="text/css" media="screen, print">
{$ticketPrintCSS}
    </style>


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

<body onload="">
<style media="print" type="text/css">
    #printbutton {
        display: none;
    }
</style>
<button id="printbutton" onclick="printpage()">Print Page</button>

<div class="container">

    <div id="cutout_tickets">

        {foreach from=$users_groups item=group name=grouploop key=groupid}
            {foreach from=$group item=user key=userid name=usersloop}
                <div class="cutout_ticket">
                    {$preTicketHTML}
                    {if $networksettings.printSSID}
                        <span class="ticket_item_label">{t}Wireless Network{/t}:</span>
                        <span class='info_username  last'>{$networksettings.printSSID}</span>
                        <br/>
                    {/if}
                    <span class="ticket_item_label ">{t}Username{/t}:</span> <span
                            class='info_username  last'>{$user.Username}</span><br/>
                    <span class="ticket_item_label ">{t}Password{/t}:</span> <span
                            class='info_password  last'>{$user.Password}</span><br/>
                    {if $networksettings.printGroup}
                        <span class="ticket_item_label ">{t}Voucher Type{/t}:</span>
                        <span class='info_username  last'>{$groupsettings.$groupid.GroupLabel}</span>
                        <br/>
                    {/if}
                    {if $networksettings.printExpiry && $user.FormatExpiration != '--'}
                        <span class="ticket_item_label ">{t}Expiry{/t}:</span>
                        <span class='info_expiry  last'>{$user.FormatExpiration}</span>
                        <br/>
                    {/if}
                    {$postTicketHTML}
                </div>
            {/foreach}
        {/foreach}

    </div>

    <div id="generated" class="">
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
