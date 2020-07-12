{include file="header.tpl" Name="Upload Logo" activepage="uploadlogo"}

{include file="errors.tpl"}

<h2>{t}Upload Logo{/t}</h2>

<div id="LogoForm">
<form method="post" action="?" class="generalForm" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="50960" />
    <div>
        <label for='newlogo'>{t}Logo Image{/t}</label>
        <input type="file" name="newlogo" id="newlogo" />
        <span>{t}Select new logo file.{/t} {t}Logo file needs to be in png format, and relatively small (remember, every page has the logo). 10 Kilobytes maximum. It's physical size shouldn't be bigger than about 220px in both directions.{/t}</span><br/>
        <img src="/grase/images/logo.png" alt="Logo"/>
    </div>

    <button type="submit" name="newlogosubmit">{t}Upload Logo{/t}</button>

</form>

{t}For more advanced logos, (different file type, size, placement}, manually upload the file to /var/www/ or /usr/share/grase/www/images/ and use the custom CSS to override this logo. The logo is defined in the #page container as the background.{/t}
{literal}
<pre>
#page {
    background: url("/grase/images/logo.png") right top no-repeat;
}
</pre>
<hr/>
<pre>
#page {
    background: none;
}
</pre>
{/literal}

</div>

{include file="footer.tpl"}
