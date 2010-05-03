{include file="header.tpl" Name="Site Settings" activepage="settings" helptext="Use this page to change settings for this Hotspot Site"}

<div id="LocationChangeForm">
<h2>Location Name</h2>
{if $error_locationname}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_locationname}</span> </div>{/if}
<form method='post' id='locationChange' action=''> 
<table>
<tr><td>Location Name</td><td><input type="text" name="newlocationname" value='{$location}'/><button type="submit" name="changelocationsubmit" value="Change Location Name">Change Location Name</button></td></tr>
</table>
</form> 

<h2>Logo</h2>
{if $error_logo}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_logo}</span> </div>{/if}
<form method='post' enctype="multipart/form-data" id='logoChange' action=''> 
<table>
<tr><td colspan="2">Logo file needs to be in png format, and relatively small (remember, every page has the logo). 10 Kilobytes maximum. It's physical size shouldn't be bigger than about 220px in both directions.</td></tr>
<tr><td>Logo File</td><td> <input type="hidden" name="MAX_FILE_SIZE" value="20480" />
<input type="file" name="newlogo" /><button type="submit" name="changelogosubmit" value="Upload New Logo">Upload New Logo</button></td></tr>
<tr><td></td><td><img src="/images/logo.png" alt="Logo"/></td></tr>
</table>
</form> 

{if $error_website}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_website}</span> </div>{/if}
<form method='post' id='websiteChange' action=''> 
<table>
<tr><td>Website details for footer link</td><td></td></tr>
<tr><td>Website Name</td><td><input type="text" name="newwebsitename" value='{$website_name}'/></td></tr>
<tr><td>Website Link<br/>(http: but no spaces allowed)</td><td><input type="text" name="newwebsitelink" value='{$website_link}'/></td></tr>
<tr><td></td><td><button type="submit" name="changewebsitesubmit" value="Change Website Details">Change Website Details</button></td></tr>
</table>
</form> 
</div>

<div id="SupportContactForm">
<h2>Support Contact</h2>
{if $error_support}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_support}</span> </div>{/if}
<form method='post' id='supportChange' action=''> 
<table>
<tr><td>Support Contact Name</td><td><input type="text" name="newsupportname" value='{$support_name}'/></td></tr>
<tr><td>Support Link<br/>(mailto: or http: but no spaces allowed)</td><td><input type="text" name="newsupportlink" value='{$support_link}'/></td></tr>
<tr><td></td><td><button type="submit" name="changesupportsubmit" value="Change Support Contact Details">Change Support Contact Details</button></td></tr>
</table>
</form> 
</div>

<div id="MoneyChangeForm">
<h2>Pricing</h2>
{if $error_pricing}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_pricing}</span> </div>{/if}
<form method='post' id='pricingChange' action=''> 
<table>
<tr><td>Price/Mb</td><td><input type="text" name="newpricemb" value='{$pricemb}'/></td></tr>
<tr><td>Price/Minute</td><td><input type="text" name="newpricetime" value='{$pricetime}'/></td></tr>
<tr><td>Currency</td><td>{html_options name=newcurrency options=$CurrencySymbols selected=$currency}</td></tr>
<tr><td colspan='2'><span>Current costs are {$dispcurrency}{$pricemb} per 1 Mb and {$dispcurrency}{$pricetime} per 1 minute</span></td></tr>
<tr><td></td><td><button type="submit" name="changepricingsubmit" value="Change Pricing">Change Pricing</button></td></tr>
</table>
</form> 
</div>

<div id="DataChangeForm">
<h2>Data "limits" (for graphs)</h2>
{if $error_data}<div class="errorPage" style="display:block;"> <span class="errorMessage">{$error_data}</span> </div>{/if}
<form method='post' id='dataChange' action=''> 
<table>
<tr><td>Sellable Data</td><td><input type="text" name="newsellable_data" value='{$sellable_data}'/></td></tr>
<tr><td>Useable Data</td><td><input type="text" name="newuseable_data" value='{$useable_data}'/></td></tr>
<tr><td></td><td><button type="submit" name="changedatasubmit" value="Change Data Limits">Change Data Limits</button></td></tr>
</table>
</form> 
</div>


{include file="footer.tpl"}
