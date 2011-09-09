{include file="header.tpl" Name="Login" activepage="portal"}

<div id="page">
{if !$hideheader}
<h1>{$logintitle}</h1>
{/if}
{*
<p>{t location=$Location}Welcome to the %1 Hotspot. Please read the following before logging in.{/t}</p>*}
{if !$hidehelplink}
<p><a href="help">{t}Information and Help{/t}</a></p>
{/if}
{if !$hidelogoutbookmark}
<p>{t href="http://10.1.0.1:3990/logoff" escape=no}For a quick logout, bookmark <a href="%1">LOGOUT</a>{/t}</p>
{/if}


{$tpl_loginhelptext}


{if $user_url}<p id="userurlnojs" style="text-align: center;">If you are already logged in, continue to your site <br/><a href="{$user_url}" style="font-size: smaller">'{$user_url|truncate:60}'</a></p>{/if}


<div id="loginerrorcontainer">
{if $error}
			<div class="ui-widget" id="errormessages">
				<div class="ui-state-error ui-corner-all"  style="margin-top: 20px; padding: 0pt 0.7em;" > 
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span></p>
					<ul>{foreach from=$error item=msg}<li><strong>{$msg}</strong></li>{/foreach}</ul>

				</div>
			<!-- close errormessages div -->
			</div>
{/if}

    <div id="loginformcontainer">
    <form method="post" action="nojslogin.php" id="logonFormnojs" class="generalForm" autocomplete="off"><!-- TODO: Make this submit over SSL -->
    {if ! $jsdisabled}
        <div class="ui-widget" id="jswarningwidget">
            <div id="nojswarning" class="ui-state-error ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p>You are using the less secure login method.<br/>{if ! $nojs}If you have javascript disabled, please try enabling it for the secure login method.<br/>{/if}Use this less secure login form if the javascript version is giving you trouble</p>
            {if $nojs}
            <p>You have disabled the secure javascript login method.<br/><a href="?enablejs">Click here to re-enable it</a></p>
            {/if}
            </div>
        <!-- close jswarningwidget div-->
        </div>
    {/if}
        <div>
            <label for='usernamenojs'>Username</label>
            <input type="text" name="username" id="usernamenojs" autofocus="autofocus"/>
            <span id="UsernameInfo">&nbsp;</span>
        </div>
        <div>
            <label for='passwordnojs'>Password</label>
            <input type="password" name="password" id="passwordnojs" />
            <span id='PasswordInfo'>&nbsp;</span>
            
        </div>    
            
        <input type="hidden" name="userurl" value="{$user_url}"/>
        <input type="hidden" name="challenge" value="{$challenge}"/>        
        <button type="submit" name="submit" id="submitbuttonnojs" class="fg-button ui-state-default ui-corner-all">Login</button>        

    
    </form>

    <!-- close loginformcontainer div -->
    </div>
    
<!-- close loginerrorcontainer div -->    
</div>

{$tpl_belowloginhtml}

<div style="clear: left; clear: right">&nbsp;</div>

{if $js}
<script type="text/javascript" id='chillijs' src='http://10.1.0.1/grase/uam/chilli.js'></script>
<p style="font-size: smaller">Trouble logging in? <a href="?disablejs">Click here to disable the javascript login forms.</a><br/>This will use a less secure login method</p>
{/if}

<!-- close page div? -->
</div>
{include file="footer.tpl"}
