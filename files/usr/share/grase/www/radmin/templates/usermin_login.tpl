{include file="header.tpl" Name="Login" activepage="login"}

<div id="loginForm">
<h2>Login</h2>
{include file="errors.tpl"}
Login is required to access the {$Application} section of this website.

<form method='post' action="?" class='generalForm' class="width1">

<div>
    <label for='username'>Username</label>
    <input type="text" name="username" value='{$username}'/>
    <span></span>
</div>

<div>
    <label for='password'>Password</label>
    <input type="password" name="password" />
    <span></span>    
</div>
<button type="submit" class="positive" name="login" value="Login">Login</button>

</form>

</div>


{include file="footer.tpl"}
