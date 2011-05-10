{include file="header.tpl" Name="Help" activepage="help"}

<div id="page">
<h1>{$Location} Hotspot - {t}Help{/t}</h1>

<p><a href="hotspot">Return to Welcome Page</a></p>
<p>For payment and an account, please contact the Office during office hours. For support <a href="{$Support.link}">contact {$Support.name}</a></p>
<p>{t href='http://10.1.0.1:3990/logoff' escape=no}For a quick logout, bookmark <a href="%1">LOGOUT</a>, this link will instantly log you out, and return you to the Welcome page.{/t}<br/>
To get back to the status page, bookmark ether the Non javascript version (<a href="./nojsstatus" target="grasestatus">Hotspot Status nojs</a>), or the preferred javascript version (<a href="javascript: loginwindow = window.open('http://10.1.0.1/grase/uam/mini', 'grasestatus', 'width=300,height=400,location=no,directories=no,status=yes,menubar=no,toolbar=no'); loginwindow.focus();">Hotspot Status</a>). You can just drag ether link to your bookmark bar to easily bookmark them.</p>

<p>{t}Your Internet usage is limit by the amount of data that flows to and from your computer, or the amount of time spent online (depending on what you account type is). To maximise your account, you may wish to do the following:{/t}</p>
<ul>
	<li>{t}Browse with images turned off{/t}</li>
	<li>{t}Resize all photos before uploading (800x600 is a good size for uploading to the internet, or emailing){/t}</li>
	<li>{t}Ensure antivirus programs do not attempt to update the program (you probably still want them to update the virus definition files).{/t}</li>
	<li>{t}Use a client program for email instead of using webmail. {/t}</li>
	<li>{t}Ensure when you finish using the Internet, you click logout so that other users won't be able to use your account{/t}</li>
</ul>
<div style="width: 45%; float: left">
	{include file="laptop_req.tpl"}
</div>
<div style="width: 45%; float: right">
	{include file="freedownloads.tpl"}
</div>
<div style="clear: left; clear: right">&nbsp;</div>


{include file="footer.tpl"}
