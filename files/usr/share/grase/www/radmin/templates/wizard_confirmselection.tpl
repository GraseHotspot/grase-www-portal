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
    <script type="text/javascript" src="/grase/vendor/jquery/dists/jquery.min.js"></script>

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

			
		});
	{/literal}		
	</script>

<body>

<h2>Confirm Selection</h2>

<form action="" method="POST">

{t}Please select your preferred payment method{/t}

You have choosen the {$selectedvoucher} voucher and wish to pay via {$selectedgateway}.

Is this correct?

<button type="submit" name="selectionconfirmed" value="correct">Yes</button>
<button type="submit" name="restartwizard" value="incorrect">Start Over</button>

</form>

</body>

</html>
