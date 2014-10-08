{include file="header.tpl" Name="DHCP Leases" activepage="dhcpleases" helptext=""}

<h2>{t}DHCP Leases{/t}</h2>

<table>
    <thead>
    <tr>
        <th>{t}IP Address{/t}</th>
        <th>{t}MAC Address{/t}</th>
        <th>{t}DHCP State{/t}</th>
        <th>{t}UserName{/t}</th>
    </tr>
    </thead>
{foreach from=$chilliSessions item=session}
    <tr>
        <td>{$session.ipAddress}</td>
        <td>{$session.macAddress}</td>
        <td>{$session.dhcpState}</td>
        <td>{$session.session.userName}</td>
    </tr>
{/foreach}
</table>

{include file="footer.tpl"}
