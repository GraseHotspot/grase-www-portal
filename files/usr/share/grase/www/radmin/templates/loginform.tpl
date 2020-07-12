{include file="header.tpl" Name="Login" activepage="login"}

<div id="loginForm">
<h2>{t}Login{/t}</h2>
{t}Login is required to access the Administration section of this website.{/t}

<form method='post' action="login.php?page={$smarty.server.SCRIPT_NAME}" class='generalForm width2'>

<div>
    <label for='username' class='width1'>{t}Username{/t}</label>
    <input type="text" name="username" value='{$username|escape}' id="username" autofocus="autofocus" required="required"/>
    <span>{t}Username for Admin interface (Different to internet access username){/t}</span>
</div>

<div class='width1'>
    <label for='password'>{t}Password{/t}</label>
    <input type="password" name="password" id="password" required="required"/>
    <span></span>
</div>
<button type="submit" class="positive" name="login" value="Login">{t}Login{/t}</button>

</form>

</div>


{include file="footer.tpl"}
