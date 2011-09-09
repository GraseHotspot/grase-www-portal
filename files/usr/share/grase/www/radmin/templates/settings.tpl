{include file="header.tpl" Name="Site Settings" activepage="settings" helptext="Use this page to change settings for this Hotspot Site"}

<h2>{t}Site Settings{/t}</h2>

<div id="SettingsForm">
<form method="post" action="?" class="generalForm">

    <div>
        <label for='locationname'>{t}Location Name{/t}</label>
        <input type="text" name="locationname" id="locationname" value='{$location}'/>
        <span id="locationnameInfo">{t}Enter a name that identifies this Hotspot Location{/t}</span>
    </div>
        
    <div>
        <label for='supportcontact'>{t}Support Contact Name{/t}</label>
        <input type="text" name="supportcontact" id="supportcontact" value='{$support_name}'/>
        <span id="supportcontactInfo">{t}Enter the name of the Support Contact{/t}</span>
    </div>
    
    <div>
        <label for='supportlink'>{t}Support Link{/t}</label>
        <input type="url" name="supportlink" id="supportlink" value='{$support_link}'/>
        <span id="supportlinkInfo">{t}This is the link for the support contact. http:// or mailto: are allowed. If using http:// ensure this is accessabile for users who aren't logged into the hotspot{/t}</span>
    </div>    


    <div>
        <label for='locale'>{t}Locale{/t}</label>
        <input name="locale" type="text" id="locale" value="{$locale}"/>
        <span id="localeInfo">{t}Select the appropriate Locale for your location{/t}
        <span class="helptext">{t escape=no}A locale has 2 parts, the language and location.<br/><strong>en_AU</strong> for example has the Language set to English, and the location to Australia.<br/><strong>en_ZA</strong> has the language set to English, and the location to South Africa, while <strong>af_ZA</strong> has the language set to Afrikaans and the location to South Africa.{/t}</span>
        <br/><strong>{t}The locale defines the number formats, currency and language. If the language selected is not available, it will fallback to English.{/t}</strong></span>
        <br/><span>{t escape=no locale="<strong>$locale</strong>" language="<strong>$language</strong>" location="<strong>$region</strong>"}Current locale is %1, which sets the language to %2, and location to %3.{/t} {t currency="<strong>$currency</strong>" escape=no}Currency symbol is %1{/t}</span>
    </div>

    <div>
        <label for='pricemb'>Cost per Mb</label>
        <input type="text" name="pricemb" id="pricemb" value='{$pricemb}'/>
        <span id="pricembInfo">{t}How much to charge per Mb{/t}</span>
    </div>


    <div>
        <label for='pricetime'>{t}Cost per Minute{/t}</label>
        <input type="text" name="pricetime" id="pricetime" value='{$pricetime}'/>
        <span id="pricetimeInfo">{t}How much to charge per Minute{/t}</span>
    </div>

    <div>
        <label for='websitename'>{t}Website Name{/t}</label>
        <input type="text" name="websitename" id="websitename" value='{$website_name}'/>
        <span id="websitenameInfo">{t}Label for Website Footer link{/t}</span>
    </div>
    
    <div>
        <label for='websitelink'>{t}Website Link{/t}</label>
        <input type="url" name="websitelink" id="websitelink" value='{$website_link}'/>
        <span id="websitelinkInfo">{t}Link for Website Footer Link{/t}</span>
    </div>   
    
    <div>
        <label for='sellable_data'>{t}Sellable Graph Max{/t}</label>
        {html_options name=sellable_data id="sellable_data" options=$gbvalues selected=$sellable_data}
        <span id="sellable_dataInfo">{t}Select what is 100% on the Sellable Data Graph{/t}</span>
    </div>     

    <div>
        <label for='useable_data'>{t}Useable Graph Max{/t}</label>
        {html_options name=useable_data id="useable_data" options=$gbvalues selected=$useable_data}
        <span id="useable_dataInfo">{t}Select what is 100% on the Useable Data Graph{/t}</span>
    </div>    
   
    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
