{include file="header.tpl" Name="Site Settings" activepage="settings" helptext="Use this page to change settings for this Hotspot Site"}

<h2>{t}Site Settings{/t}</h2>

<div id="SettingsForm">
<form method="post" action="" class="generalForm">

    <div>
        <label for='locationname'>{t}Location Name{/t}</label>
        <input type="text" name="locationname" value='{$location}'/>
        <span id="locationnameInfo">{t}Enter a name that identifies this Hotspot Location{/t}</span>
    </div>
        
    <div>
        <label for='supportcontact'>{t}Support Contact Name{/t}</label>
        <input type="text" name="supportcontact" value='{$support_name}'/>
        <span id="supportcontactInfo">{t}Enter the name of the Support Contact{/t}</span>
    </div>
    
    <div>
        <label for='supportlink'>{t}Support Link{/t}</label>
        <input type="text" name="supportlink" value='{$support_link}'/>
        <span id="supportlinkInfo">{t}This is the link for the support contact. http:// or mailto: are allowed. If using http:// ensure this is accessabile for users who aren't logged into the hotspot{/t}</span>
    </div>    


    <div>
        <label for='currency'>{t}Currency{/t}</label>
        {html_options name=currency options=$CurrencySymbols selected=$currency}
        <span id="currencyInfo">{t}Select the appropriate symbol for your local currency{/t}</span>
    </div>


    <div>
        <label for='pricemb'>Cost per Mb</label>
        <input type="text" name="pricemb" value='{$pricemb}'/>
        <span id="pricembInfo">{t}How much to charge per Mb{/t}</span>
    </div>


    <div>
        <label for='pricetime'>{t}Cost per Minute{/t}</label>
        <input type="text" name="pricetime" value='{$pricetime}'/>
        <span id="pricetimeInfo">{t}How much to charge per Minute{/t}</span>
    </div>

    <div>
        <label for='websitename'>{t}Website Name{/t}</label>
        <input type="text" name="websitename" value='{$website_name}'/>
        <span id="websitenameInfo">{t}Label for Website Footer link{/t}</span>
    </div>
    
    <div>
        <label for='websitelink'>{t}Website Link{/t}</label>
        <input type="text" name="websitelink" value='{$website_link}'/>
        <span id="websitelinkInfo">{t}Link for Website Footer Link{/t}</span>
    </div>   
    
    <div>
        <label for='sellable_data'>{t}Sellable Graph Max{/t}</label>
        {html_options name=sellable_data options=$gbvalues selected=$sellable_data}
        <span id="sellable_dataInfo">{t}Select what is 100% on the Sellable Data Graph{/t}</span>
    </div>     

    <div>
        <label for='useable_data'>{t}Useable Graph Max{/t}</label>
        {html_options name=useable_data options=$gbvalues selected=$useable_data}
        <span id="useable_dataInfo">{t}Select what is 100% on the Useable Data Graph{/t}</span>
    </div>    
   
    <button type="submit" name="submit">{t}Save Settings{/t}</button> 

</form>

</div>

{include file="footer.tpl"}
