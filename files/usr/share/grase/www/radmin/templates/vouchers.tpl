{include file="header.tpl" Name="Vouchers" activepage="vouchers"}

<h2>{t}Voucher Config{/t}</h2>

<p>
{t}Vouchers are disabled if nether Initial Voucher, or Topup Voucher are selected.{/t} {t}Vouchers without a Time or Data limit do not inherit the default limits from the group, but any Recurring limits will apply.{/t}
</p>
<form method="post" action="?" class="generalForm">
<script type="text/javascript">
{literal}
// TODO find out of there is ANY way to make this work without javascript for backwards compatability
// Based off http://www.dwright.us/?p=472
$(document).ready(function(){
    // do when submit is pressed
    $('form').submit(function() {

        $('input:checkbox:not(:checked)').each(function() {
                console.log($(this).attr('name'));
                $(this).before($('<input>')
                .attr('class', '_temp')
                .attr('type', 'hidden')
                .attr('name', $(this).attr('name')));
                // .val('off'));
        });
        });

});
{/literal}
</script>
<table id='vouchersTable' style='display:block;'>
<thead>
<tr>
        <th>{t}Voucher Name{/t}</th>
        <th>{t}Price{/t}</th>
        <th>{t}Group{/t}</th>
        <th>{t}Data Limit{/t}</th>
        <th>{t}Time Limit{/t}</th>
        <th>{t}Initial Voucher{/t}</th>
        <th>{t}Topup Voucher{/t}</th>
        <th>{t}Description{/t}</th>
        <th></th>
</tr>
</thead>
<tbody>
{foreach from=$vouchersettings item=settings key=vouchername name=vouchersettingsloop}

<tr>
        <td><input type="text" name="vouchername[]" class="mediuminput vouchernameinput" value='{$settings.VoucherLabel}' placeholder="{t}Name{/t}"/></td>
        <td><input type="number" min='0' step='0.01' name="voucherprice[]" class="smallinput voucherpriceinput" value='{$settings.VoucherPrice}' placeholder="{t}Price{/t}"/></td>
        <td>{html_options class="mediuminput" name="vouchergroup[]" options=$groups selected=$settings.VoucherGroup}</td>
        <td>{html_options class="mediuminput" name="voucherMax_Mb[]" options=$Datavals selected=$settings.MaxMb}</td>
        <td>{html_options class="mediuminput" name="voucherMax_Time[]" id="Max_Time" options=$Timevals selected=$settings.MaxTime}</td>
        <td><input {inputtype type="bool" value=$settings.InitVoucher} class="smallinput" name="initialvoucher[]" /></td>
        <td><input  {inputtype type="bool" value=$settings.TopupVoucher} class="smallinput" name="topupvoucher[]"/></td>
        <td><textarea name="voucherdescription[]" maxlength="250">{$settings.Description}</textarea></td>
        <td><span class="jsremovetable">
                <span class="jsremovetablebutton"></span>
                <span class="jsaddtableremovetext" id="tableaddtext"></span>
            </span> </td>
</tr>
{/foreach}

<tr>
        <td><input type="text" name="vouchername[]" class="mediuminput vouchernameinput" value='' placeholder="{t}Name{/t}"/></td>
        <td><input type="number" min='0' step='0.01' name="voucherprice[]" class="smallinput voucherpriceinput" value='' placeholder="{t}Price{/t}"/></td>
        <td>{html_options class="mediuminput" name="vouchergroup[]" options=$groups selected=$user.Group}</td>
        <td>{html_options class="mediuminput" name="voucherMax_Mb[]" options=$Datavals selected=$user.Max_Mb}</td>
        <td>{html_options class="mediuminput" name="voucherMax_Time[]" id="Max_Time" options=$Timevals selected=$user.Max_Time}</td>
        <td><input type="checkbox" class="smallinput" name="initialvoucher[]"/></td>
        <td><input type="checkbox" class="smallinput" name="topupvoucher[]"/></td>
        <td><textarea name="voucherdescription[]" maxlength="250"></textarea></td>
        <td><span class="jsaddtable">
                <span class="jsaddtablebutton"></span>
                <span class="jsaddtableremovetext" id="tableaddtext"></span>
            </span> </td>
</tr>
</tbody>

</table>
    <button type="submit" name="submit">{t}Save Settings{/t}</button>

</form>
{include file="footer.tpl"}
