<?php
// Store all the default templates in here to be loaded if no templates are stored

// helptext: displayed above login form in main portal page
$Settings->setTemplate('loginhelptext','
    <p>By logging in, you are agreeing to the following:</p>
    <ul>
	    <li><strong>All network activity will be monitored, this includes: websites, bandwidth usage, protocols</strong></li>
	    <li><strong>You will not access sites containing explicit or inappropriate material</strong></li>
	    <li><strong>You will not attempt to access any system on this network</strong></li>
    </ul>
');

// helptext: page contents of info & help file
$Settings->setTemplate('helptext',
'<p>For payment and an account, please contact the Office during office hours.</p>
<p>For a quick logout, bookmark <a href="http://10.1.0.1:3990/logoff">LOGOUT</a>, this link will instantly log you out, and return you to the Welcome page.<br/>
To get back to the status page, bookmark ether the Non javascript version (<a href="./nojsstatus" target="grasestatus">Hotspot Status nojs</a>), or the preferred javascript version (<a href="javascript: loginwindow = window.open("http://10.1.0.1/grase/uam/mini", "grasestatus", "width=300,height=400,location=no,directories=no,status=yes,menubar=no,toolbar=no"); loginwindow.focus();">Hotspot Status</a>). You can just drag ether link to your bookmark bar to easily bookmark them.</p>

<p>Your Internet usage is limit by the amount of data that flows to and from your computer, or the amount of time spent online (depending on what you account type is). To maximise your account, you may wish to do the following:</p>
<ul>
	<li>Browse with images turned off</li>
	<li>Resize all photos before uploading (800x600 is a good size for uploading to the internet, or emailing)</li>
	<li>Ensure antivirus programs do not attempt to update the program (you probably still want them to update the virus definition files).</li>
	<li>Use a client program for email instead of using webmail.</li>
	<li>Ensure when you finish using the Internet, you click logout so that other users won\'t be able to use your account</li>
</ul>
');

// maincss: main css override for login portal
$Settings->setTemplate('maincss','');

// loggedinnojshtml: html to show on successful login
$Settings->setTemplate('loggedinnojshtml', '
<p>Your login was successful. Please click <a href="nojsstatus" target="grasestatus">HERE</a> to open a status window<br/>If you don\'t open a status window, then bookmark the link <a href="http://logout/">http://logout/</a> so you can logout when finished.</p>
');

$results += 4;
?>
