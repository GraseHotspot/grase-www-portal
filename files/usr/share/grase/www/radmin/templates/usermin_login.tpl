{include file="header.tpl" Name="Login" activepage="login"}

<div id="loginForm">
<h2>{t}Login{/t}</h2>
Login is required to access the {$Application} section of this website.

<form method='post' action="?" class='generalForm' class="width1">

<div>
    <label for='username'>{t}Username{/t}</label>
    <input type="text" name="username" value='{$username}' autofocus="autofocus" required="required"/>
    <span></span>
</div>

<div>
    <label for='password'>{t}Password{/t}</label>
    <input type="password" name="password" required="required" />
    <span></span>
</div>
<button type="submit" class="positive" name="login" value="Login">{t}Login{/t}</button>

</form>

</div>


{include file="footer.tpl"}
