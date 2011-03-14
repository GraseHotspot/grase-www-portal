{include file="header.tpl" Name="Login" activepage="login"}

<div id="loginForm">
<h2>Login</h2>
<div class="errorPage" style="display: {if $error}block;{else}none;{/if}"><span id="errorMessage">{foreach from=$error item=msg}{$msg}<br/>{/foreach}</span> </div>
Login is required to access the Administration section of this website.
<form method='post' action="login?page={$smarty.server.SCRIPT_NAME}">
<table>
<tr><td>Username</td><td> <input type="text" name="username" value='{$username}'/></td></tr>
<tr><td>Password</td><td> <input type="password" name="password" value='{$password}'/></td></tr>
<tr><td></td><td><button type="submit" class="positive" name="login" value="Login">Login</button></td></tr>
</table>
</form>

</div>


{include file="footer.tpl"}
