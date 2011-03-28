{include file="header.tpl" Name="Site Settings" activepage="settings" helptext="Use this page to change settings for this Hotspot Site"}

<div id="SettingsForm">
<form method="post" action="" class="generalForm">

    <div>
        <label for='locationname'>Location Name</label>
        <input type="text" name="locationname" value='{$location}'/>
        <span id="locationnameInfo">Enter a name that identifies this Hotspot Location</span>
    </div>
        
    <div>
        <label for='supportcontact'>Support Contact Name</label>
        <input type="text" name="supportcontact" value='{$support_name}'/>
        <span id="supportcontactInfo">Enter the name of the Support Contact</span>
    </div>
    
    <div>
        <label for='supportlink'>Support Link</label>
        <input type="text" name="supportlink" value='{$support_link}'/>
        <span id="supportlinkInfo">This is the link for the support contact. http:// or mailto: are allowed. If using http:// ensure this is accessabile for users who aren't logged into the hotspot</span>
    </div>    


    <div>
        <label for='currency'>Currency</label>
        {html_options name=currency options=$CurrencySymbols selected=$currency}
        <span id="currencyInfo">Select the appropriate symbol for your local currency</span>
    </div>


    <div>
        <label for='pricemb'>Cost per Mb</label>
        <input type="text" name="pricemb" value='{$pricemb}'/>
        <span id="pricembInfo">How much to charge per Mb</span>
    </div>


    <div>
        <label for='pricetime'>Cost per Minute</label>
        <input type="text" name="pricetime" value='{$pricetime}'/>
        <span id="pricetimeInfo">How much to charge per Minute</span>
    </div>

    <div>
        <label for='websitename'>Website Name</label>
        <input type="text" name="websitename" value='{$website_name}'/>
        <span id="websitenameInfo">Label for Website Footer link</span>
    </div>
    
    <div>
        <label for='websitelink'>Website Link</label>
        <input type="text" name="websitelink" value='{$website_link}'/>
        <span id="websitelinkInfo">Link for Website Footer Link</span>
    </div>   
    
    <div>
        <label for='sellable_data'>Sellable Graph Max</label>
        {html_options name=sellable_data options=$gbvalues selected=$sellable_data}
        <span id="sellable_dataInfo">Select what is 100% on the Sellable Data Graph</span>
    </div>     

    <div>
        <label for='useable_data'>Useable Graph Max</label>
        {html_options name=useable_data options=$gbvalues selected=$useable_data}
        <span id="useable_dataInfo">Select what is 100% on the Useable Data Graph</span>
    </div>    
   
    <button type="submit" name="submit">Save Settings</button> 

</form>

</div>

{include file="footer.tpl"}
