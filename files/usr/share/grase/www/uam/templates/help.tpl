{include file="header.tpl" Name="Help" activepage="help"}

<div id="page">
<h1>{$Location} Hotspot - Help</h1>

<p><a href="?">Return to Welcome Page</a></p>
<p>For payment and an account, please contact the Office during office hours. For support <a href="{$Support.link}">contact {$Support.name}</a></p>
<p>For a quick logout, bookmark <a href="http://1.0.0.0/">LOGOUT</a>, this link will instantly log you out, and return you to the Welcome page.<br/>
To get back to the status page, bookmark this page, then you can press the login button again to get the status window up.</p>
<p>Your Internet usage is limit by the amount of data that flows to and from your computer, or the amount of time spent online (depending on what you have purchased from the office). To maximise your account, you may wish to do the following:</p>
<ul>
	<li>Browse with images turned off</li>
	<li>Resize all photos before uploading (800x600 is a good size for uploading to the internet, or emailing)</li>
	<li>Ensure antivirus programs do not attempt to update the program (you probably still want them to update the virus definition files). Many antivirus update sites are block automatically</li>
	<li>Use a client program for email instead of using webmail. (<a target="_blank" href="http://gmail.com">Gmail</a> has excellent support for client email programs)</li>
	<li>Ensure when you finish using the Internet, you click logout so that other users won't be able to use your account</li>
</ul>
<p>Note: The following things are blocked, if you need to download files of these types, please go to the Internet cafe.</p>
<ul>
	<li>All music. This includes MP3's, and Pod casts</li>
	<li>All videos/movies. This includes Flash Videos (i.e. youtube)</li>
	<li>All executable file formats. This includes windows updates, screensavers, and many files that can contain viruses</li>
	<li>Website advertisements. This mainly blocks large ads, and popups. You may find that sometimes clicking a link will open a new windows that is blocked. This is a URL redirector, and the only way to access the site you want, is to find the direct link to the site (you may need to google it)</li>
</ul>
<div style="width: 45%; float: left">
	{include file="laptop_req.tpl"}
</div>
<div style="width: 45%; float: right">
	{include file="freedownloads.tpl"}
</div>
<div style="clear: left; clear: right">&nbsp;</div>


{include file="footer.tpl"}
