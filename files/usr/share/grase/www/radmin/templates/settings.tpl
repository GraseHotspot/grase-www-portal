{include file="header.tpl" Name="Site Settings" activepage="settings" helptext="Use this page to change settings for this Hotspot Site"}

<h2>{t}Site Settings{/t}</h2>

<div id="SettingsForm">
<form method="post" action="?" class="generalForm">

    <div>
        <label for='locationname'>{t}Location Name{/t}</label>
        <input type="text" name="locationname" id="locationname" value='{$location|escape}'/>
        <span id="locationnameInfo">{t}Enter a name that identifies this Hotspot Location{/t}</span>
    </div>

    <div>
        <label for='supportcontact'>{t}Support Contact Name{/t}</label>
        <input type="text" name="supportcontact" id="supportcontact" value='{$support_name|escape}'/>
        <span id="supportcontactInfo">{t}Enter the name of the Support Contact{/t}</span>
    </div>

    <div>
        <label for='supportlink'>{t}Support Link{/t}</label>
        <input type="url" name="supportlink" id="supportlink" value='{$support_link|escape}'/>
        <span id="supportlinkInfo">{t}This is the link for the support contact. http:// or mailto: are allowed. If using http:// ensure this is accessabile for users who aren't logged into the hotspot{/t}</span>
    </div>


    <div>
        <label for='locale'>{t}Locale{/t}</label>
        <input name="locale" type="text" id="locale" value="{$locale|escape}"/>
                <span class="helptext ui-icon-help">{t}Available languages:{/t}
            <dl>
                {foreach from=$available_languages item=languageitem}
                <dt>{$languageitem.code}</dt> <dd>{$languageitem.display}</dd>
                {/foreach}
            </dl>
        </span>

        <span id="localeInfo">{t}Select the appropriate Locale for your location{/t}
        <span class="helptext">{t escape=no}A locale has 2 parts, the language and location.<br/><strong>en_AU</strong> for example has the Language set to English, and the location to Australia.<br/><strong>en_ZA</strong> has the language set to English, and the location to South Africa, while <strong>af_ZA</strong> has the language set to Afrikaans and the location to South Africa.{/t}</span>
        <br/><strong>{t}The locale defines the number formats, currency and language. If the language selected is not available, it will fallback to English.{/t}</strong></span>
        <br/><span>{t escape=no locale="<strong>$locale</strong>" language="<strong>$language</strong>" location="<strong>$region</strong>"}Current locale is %1, which sets the language to %2, and location to %3.{/t} {t currency="<strong>$currency</strong>" escape=no}Currency symbol is %1{/t}</span>
    </div>

    <div>
        <label for='mboptions'>{t}Data Options{/t}</label>
        <input type="text" name="mboptions" id="mboptions" value='{$mboptions}'/>
        <span id="mboptionsInfo">{t}Space separated list of Data Options in MB's to populate dropdown boxes{/t}</span>
    </div>


    <div>
        <label for='timeoptions'>{t}Time Options{/t}</label>
        <input type="text" name="timeoptions" id="timeoptions" value='{$timeoptions}'/>
        <span id="timeoptionsInfo">{t}Space separated list of Time options in minutes to populate dropdown boxes.{/t}</span>
    </div>

    <div>
        <label for='bwoptions'>{t}Bandwidth Options{/t}</label>
        <input type="text" name="bwoptions" id="bwoptions" value='{$bwoptions}'/>
        <span id="bwoptionsInfo">{t}Space separated list of Bandwidth options in kbit/s to populate dropdown boxes.{/t}</span>
    </div>

    <div>
        <label for='websitename'>{t}Website Name{/t}</label>
        <input type="text" name="websitename" id="websitename" value='{$website_name|escape}'/>
        <span id="websitenameInfo">{t}Label for Website Footer link{/t}</span>
    </div>

    <div>
        <label for='websitelink'>{t}Website Link{/t}</label>
        <input type="url" name="websitelink" id="websitelink" value='{$website_link|escape}'/>
        <span id="websitelinkInfo">{t}Link for Website Footer Link{/t}</span>
    </div>

    <button type="submit" name="submit">{t}Save Settings{/t}</button>

</form>

</div>

{include file="footer.tpl"}
