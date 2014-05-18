<!DOCTYPE html>
<html>
<head>
<title>{$Title} - {t}{$Name}{/t}</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<meta name="generator" content="{$Application} {$application_version}" />
<!-- CSS Stylesheet -->
<link rel="stylesheet" type="text/css" href="/grase/hotspot.css?{$hotspotcssversion}" id="hotspot_css" />
<link rel="stylesheet" type="text/css" href="radmin.css?{$radmincssversion}" id="radmin_css" />
<link rel='stylesheet' type="text/css" href='wizard.css'/>
    <script type="text/javascript" src="/grase/vendor/jquery/dist/jquery.min.js"></script>

	<script type="text/javascript">
	{literal}
		$(function() {
			$("a.voucher").click(function() { //check for the first selection
				var $column = $(this).attr('title'); // assign the ID of the column
				$('div.vouchertype').children().find("li").removeClass("highlight") //forget the last highlighted column
				$('div.vouchertype').children().find("li."+$column).addClass("highlight"); //highlight the selected column
				$('div.vouchertype').children().find("li."+$column).find(":radio").attr("checked","checked");
				return false;
			});
			
			$("a.gateway").click(function() { //check for the first selection
				var $column = $(this).attr('title'); // assign the ID of the column
				$('div.paymentgateways').children().find("li").removeClass("highlight") //forget the last highlighted column
				$('div.paymentgateways').children().find("li."+$column).addClass("highlight"); //highlight the selected column
				$('div.paymentgateways').children().find("li."+$column).find(":radio").attr("checked","checked");
				return false;
			});			

			
		});
	{/literal}		
	</script>

<body>

<h1>Initial page for wizard</h1>

{$error}

<form action="" method="POST">

<h2>{t}Please select your voucher type{/t}</h2>

<div id="vouchertypes">

{foreach from=$groupsettings item=attributes key=groupname}

        <div class="vouchertype">
                <h3>{$attributes.GroupLabel}</h3>
                {$attributes.Comment}
                <hr/>
                <ul>
                        {foreach from=$vouchers.$groupname item=voucher}
                        <li class="{$voucher.VoucherName}">
                        <a href="#" class="signup voucher" title="{$voucher.VoucherName}">
                        <label>{$voucher.VoucherLabel}
                        {$voucher.VoucherPrice|displayMoneyLocales}
                        {$voucher.Description}
                        </label>
                        <input type="radio" class="radioFancy" name="voucherselected" value="{$voucher.VoucherName}" required /></a>
                        </li>
                        {/foreach}
                </ul>
        </div>
{/foreach}

</div>

<h2>{t}Please select your preferred payment method{/t}</h2>

<div id="paymentgateways" class="paymentgateways">
        <ul>
                {foreach from=$paymentgateways item=gateway key=gatewayName}
                <li class="{$gatewayName}">
                <a href="#" class="signup gateway" title="{$gatewayName}">
                <label>{$gateway.Label}</label>
                <label>{$gateway.Description}</label>
                <input type="radio" class="radioFancy" name="gatewayselected" value="{$gatewayName}" required /></a>
                </li>
                {/foreach}
        </ul>

</div>

<input type="submit" name="gotopayment" value="{t}Proceed to Payment{/t}" />

</form>

</body>

</html>
