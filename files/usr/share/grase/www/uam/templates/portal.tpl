{if $activepage != 'mini'}
{include file="header.tpl" Name="Login" activepage="portal"}
{/if}
<div id="container">
    {if !$hideheader}
    <h1>{$logintitle}</h1>
    {/if}

    {if $tpl_termsandconditions}
    <div >
        <p style="text-align:center"><strong>{t}By continuing, you agree to the below terms and conditions.{/t}</strong></p>
        <div style="padding: 0 15px; max-width: 330px;" class="center-block"><button class="showLink visible-xs btn btn-sm btn-block"  style="max-width: 300px; margin: 0 auto; "  onclick="$('#tos').toggleClass('hidden-xs')">{t}Show Terms and Conditions{/t}</button></div>
        <div id="tos" class="hidden-xs center-block" style="height:8em;width:100%; max-width: 960px; border:1px solid #ccc;overflow:auto;"><!-- Terms and Conditions -->{$tpl_termsandconditions}</div>
    </div>
    {/if}

    {if $user_url}{if $js}<noscript>{/if}<p id="userurlnojs" style="text-align: center;">{t}If you are already logged in, continue to your site{/t} <br/><a href="{$user_url}" style="font-size: smaller">'{$user_url|truncate:60}'</a></p>{if $js}</noscript>{/if}{/if}

    {if $error}
    <div class="ui-widget" id="errormessages">
        <div class="ui-state-error ui-corner-all"  style="margin-top: 20px; padding: 0 0.7em;" >
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span></p>
            <ul>{foreach from=$error item=msg}<li><strong>{$msg}</strong></li>{/foreach}</ul>
        </div>
    <!-- close errormessages div -->
    </div>
    {/if}

    <!-- Voucher Error messages (not found, expired, etc) will be displayed using this variable - do not remove -->
    <div class="error center-block" id="errormessages" style="margin-top: 1em; max-width: 300px; text-align: center;"></div>

    {if $automac}
    <div class="" style="" id="tosaccept">
        <form class="form-signin" method="get" action="//{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}&automac=1">
            <h2>{t}Free Access{/t}</h2>
            <button class="btn btn-success btn-block" type="submit">{t}Free Access{/t}</button>
        </form>
    </div>{/if}

    <div id="loginform">
        <form method="post"  action="nojslogin.php" autocomplete="off" class="form-signin">
            <h2>{t}Voucher Login{/t}</h2>
            <input class="form-control" id="username" name="username" type="text" required autofocus placeholder="{t}Username{/t}"/>
            <input class="form-control" id="password" name="password" type="password" required placeholder="{t}Password{/t}"/>
            <input type="hidden" name="challenge" value="{$challenge|escape}"/>
            <input type="hidden" name="response" value=""/>
            <input type="hidden" name="userurl" value="{$user_url|escape}"/>
            <button class="btn btn-primary btn-block" type="SUBMIT">Login</button>
            {if $nojs}<p style="font-size: smaller">{t}You have disabled the secure javascript login method.{/t} <a href="?enablejs">{t}Click here to re-enable it{/t}</a></p>{/if}
            </form>
    </div>

    <div id="loading" class="well center-block" style="margin-top: 1em; max-width: 300px; display: none; text-align: center">
        <i class="fa-spinner fa-spin fa fa-2x"></i> {t}Attempting Login...{/t}
    </div>

    <div id="loggedin" style="display: none">
        <span id="loggedinuserName" style="display: none">{t}Logged in as{/t} <strong id="loggedinuserNameVal"></strong></span>
        <span id="sessionTimeout" style="display: none"><br/>{t}Remaining Session Time{/t} <strong id="sessionTimeoutVal"></strong></span>
        <span id="sessionMaxTotalOctets" style="display: none"><br/>{t}Remaining Session Data{/t} <strong id="sessionMaxTotalOctetsVal"></strong></span>
        <br/><a class="btn btn-danger" href="http://1.0.0.0" id="logofflink">{t}Logout{/t}</a> {if $activepage != 'mini'}<a href="/grase/uam/mini" class="btn btn-success" target="grase_uam" id='statuslink'>{t}Open Status Window{/t} <i class="fa fa-external-link"></i></a>{/if}
    </div>

    {$tpl_belowloginhtml}

    <div style="clear: both">&nbsp;</div>

    {if $js}
        <script type="text/javascript" src="/grase/uam/js/chilliMD5.js"></script>
        <script type="text/javascript" id='chillijs' src='/grase/uam/js.php?js=jqchilli.js'></script>
        <p style="font-size: smaller; text-align: center">{t}Trouble logging in?{/t} <a href="?disablejs">{t}Click here to try a less secure login.{/t}</a></p>
    {/if}

<!-- close page div? -->
</div>
{if $activepage != 'mini'}
{include file="footer.tpl"}
{/if}
