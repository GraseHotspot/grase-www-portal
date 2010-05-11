{include file="header.tpl" Name="" activepage="mini"}

<p id="location_name" >{$Location}</p>

<div id="noLocation" style="display:none;">
<p style="padding-top: 100px;"><strong>You are not at a hotspot.</strong>
Please don't try and access this page directly.
</p>
</div>

{literal}<script type="text/javascript"><!--//--><![CDATA[//><!--

	function create_download_PB() {
		if(typeof(download_PB) == 'undefined'){
		// download_bar : multicolor (and take all other default paramters)
		download_PB = new JS_BRAMUS.jsProgressBar(
					$('download_bar'),
					100,
					{
						animate: false,
						showText: false,

						barImage	: Array(
							'images/bramus/percentImage_back4.png',
							'images/bramus/percentImage_back3.png',
							'images/bramus/percentImage_back2.png',
							'images/bramus/percentImage_back1.png'
						)

						/*onTick : function(pbObj) {

							switch(pbObj.getPercentage()) {

								case 5:
									alert('Only 5% of your download limit remaining');
								break;

							}

							return true;
						}*/
					}
				);

		// time_bar : multicolor (and take all other default paramters)
		time_PB = new JS_BRAMUS.jsProgressBar(
					$('time_bar'),
					100,
					{
						animate: false,
						showText: false,

						barImage	: Array(
							'images/bramus/percentImage_back4.png',
							'images/bramus/percentImage_back3.png',
							'images/bramus/percentImage_back2.png',
							'images/bramus/percentImage_back1.png'
						)

						/*onTick : function(pbObj) {

							switch(pbObj.getPercentage()) {

								case 5:
									alert('Only 5% of your time limit remaining');
								break;

							}

							return true;
						}*/
					}
				);
		}
	};
//--><!]]></script>{/literal}

<script id='chillijs' src='http://10.1.0.1/grase/uam/chilli.js'></script>
<!--{if $user_url}<span id='origurl'><a href="{$user_url}">Original URL {$user_url}</a></span>{/if}-->




{include file="footer.tpl" hide="true"}
