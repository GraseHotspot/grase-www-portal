{include file="header.tpl" Name="Welcome" activepage="portal"}

<div id="page">
<h1>{$Location} Hotspot - Welcome</h1>

<p>Welcome to the {$Location} Hotspot. Please read the following before logging in.</p>
<p><a href="help">Information and Help</a></p>

<p>By logging in, you are agreeing to the following:</p>
<ul>
	<li><strong>All network activity will be monitored, this includes: websites, bandwidth usage, protocols</strong></li>
	<li><strong>You will not access sites containing explicit or inappropriate material</strong></li>
	<li><strong>You will not attempt to access any system on this network</strong></li>
</ul>

{if $user_url}<p id="userurlnojs" style="text-align: center;">If you are already logged in, continue to your site <br/><a href="{$user_url}" style="font-size: smaller">'{$user_url|truncate:60}'</a></p>{/if}


<div>
{if $error}
			<div class="ui-widget" id="errormessages">
				<div class="ui-state-error ui-corner-all"  style="margin-top: 20px; padding: 0pt 0.7em;" > 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
					<ul>{foreach from=$error item=msg}<li><strong>{$msg}</strong></li>{/foreach}</ul></p>

				</div>
			</div>
{/if}
    <form method="post" action="nojslogin.php" id="logonFormnojs" class="generalForm"><!-- TODO: Make this submit over SSL --!>
        <div class="ui-widget" id="jswarningwidget">
            <div id="nojswarning" class="ui-state-error ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p>You are using the less secure login method.<br/>{if ! $nojs}If you have javascript disabled, please try enabling it for the secure login method.<br/>{/if}Use this less secure login form if the javascript version is giving you trouble</p>
            {if $nojs}
            <p>You have disabled the secure javascript login method.<br/><a href="?enablejs">Click here to re-enable it</a></p>
            {/if}
</div>
        </div>
        <div>
            <label for='username'>Username</label>
            <input type="text" name="username" />
            <span id="UsernameInfo">&nbsp;</span>
        </div>
        <div>
            <label for='password'>Password</label>
            <input type="password" name="password" />
            <span id='PasswordInfo'>&nbsp;</span>
            
        </div>    
            
        <input type="hidden" name="userurl" value="{$user_url}"/>
        <input type="hidden" name="challenge" value="{$challenge}"/>        
        <button type="submit" name="submit" id="submitbuttonnojs" onClick="connect();" class="fg-button ui-state-default ui-corner-all">Login</button>        
    </form>
    

</div>
<div style="clear: left; clear: right">&nbsp;</div>

{if $js}
<script id='chillijs' src='http://10.1.0.1/grase/uam/chilli.js'></script>
<p style="font-size: smaller">Trouble logging in? <a href="?disablejs">Click here to disable the javascript login forms.</a><br/>This will use a less secure login method</p>
{/if}

{include file="footer.tpl"}
