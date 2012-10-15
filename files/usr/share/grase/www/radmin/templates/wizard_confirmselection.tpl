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
    <script type="text/javascript" src="/grase/js/jquery/jquery-1.5.2.min.js"></script>

	<script type="text/javascript">
	{literal}
		$(function() {
			$("a.gateway").click(function() { //check for the first selection
				var $column = $(this).attr('title'); // assign the ID of the column
				$('div.paymentgateways').children().find("li").removeClass("highlight") //forget the last highlighted column
				$('div.paymentgateways').children().find("li."+$column).addClass("highlight"); //highlight the selected column
				$('div.paymentgateways').children().find("li."+$column).find(":radio").attr("checked","checked");
				return false;
			});
			/*$("a.Mbps").click(function() { //check for the second selection
				var $column = $(this).attr('title'); // assign the ID of the column
				$('table.RTMbps').children().find("td").removeClass("highlight"); //forget the last highlighted column
				$('table.RTMbps').children().find("td."+$column).addClass("highlight"); //highlight the selected column
				$('table.RTMbps').children().find("td."+$column).find(":radio").attr("checked","checked");
				return false;
			});
			$("button.sendit").click(function() {
				var $DDR = $('table.RTDDR').children().find("td").find(":checked").val();
				var $Mbps = $('table.RTMbps').children().find("td").find(":checked").val();
				alert('You selected '+$DDR+' of RAM, and '+$Mbps+' Bandwidth, for example');
				return false;
			});*/
			
		});
	{/literal}		
	</script>

<body>

<h2>Confirm Selection</h2>

<form action="" method="POST">

{t}Please select your preferred payment method{/t}

You have choosen the {$selectedvoucher} voucher and wish to pay via {$selectedpaymentgateway}.

Is this correct?

<button type="submit" name="selectionconfirmed" value="correct">Yes</button>
<button type="submit" name="selectionconfirmed" value="incorrect">Start Over</button>

</form>

</body>

</html>
