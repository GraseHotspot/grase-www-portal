{include file="header.tpl" Name="Login" activepage="login"}

<div id="loginForm">
<h2>Login</h2>
Login is required to access the Administration section of this website.

<form method='post' action="login?page={$smarty.server.SCRIPT_NAME}" class='generalForm width2'>

<div>
    <label for='username' class='width1>Username<input type="text" name="username" value='{$username}'/></label>
    <input type="text" name="username" value='{$username}'/>
    <span>Username for Admin interface (Different to internet access username)</span>
</div>

<div class='width1'>
    <label for='password'>Password</label>
    <input type="password" name="password" />
    <span></span>    
</div>
<button type="submit" class="positive" name="login" value="Login">Login</button>

</form>

</div>


{include file="footer.tpl"}
