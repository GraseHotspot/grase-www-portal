{include file="header.tpl" Name="" activepage="mini"}

    <div class="well">
        <!-- Voucher Error messages (not found, expired, etc) will be displayed using this variable - do not remove -->
        <p class="error" id="errormessages"></p>

        <div id="loginform">
            <p>Enter your access code below:</p>
            <form method="post"  action="nojslogin.php" autocomplete="off">
            <label>{t}Username{/t}</label>
            <input class="input" id="username" name="username" type="text" required placeholder="{t}Username{/t}"/>
            <label>{t}Password{/t}</label>
            <input class="input" id="password" name="password" type="password" required placeholder="{t}Password{/t}"/>
            <input type="hidden" name="challenge" value="{$challenge}"/>
            <input type="hidden" name="response" value=""/>
            <input type="hidden" name="userurl" value="{$user_url}"/>

                <br/><button class="btn" type="SUBMIT">Login</button>
            </form>
        </div>
        <div id="loading" class="well well-large well-transparent lead" style="display: none"><i class="icon-spinner icon-spin icon-2x pull-left"></i> {t}Loading...{/t}</div>
        <div id="loggedin" style="display: none">
        <span id="loggedinuserName" style="display: none">{t}Logged in as{/t} <strong id="loggedinuserNameVal"></strong></span>
        <span id="sessionTimeout" style="display: none"><br/>Remaining Time <strong id="sessionTimeoutVal"></strong></span>
        <span id="sessionMaxTotalOctets" style="display: none"><br/>Remaining Data <strong id="sessionMaxTotalOctetsVal"></strong></span>
        {* We can use # as href here as this is a javascript portal, so remaining compatible isn't as important *}
        <br/><a class="btn btn-danger" href="#" id="logofflink">{t}Logout{/t}</a>
        </div>
    </div>
    <script type="text/javascript" src="/grase/uam/js/chilliMD5.js"></script>
    <script src="/grase/uam/js/jqchilli.js"></script>




{include file="footer.tpl" hide="true"}
