{include file="header.tpl" Name="Welcome" activepage="portal"}

<div id="page">
<h1>{$Location} Hotspot - Welcome</h1>

<p>Welcome to the {$Location} Hotspot. Please read the following before logging in.</p>
<p><a href="?help">Information and Help</a></p>

<p>For payment and an account, please contact the Office during office hours</p>
<p>By logging in, you are agreeing to the following:</p>
<ul>
	<li><strong>All network activity will be monitored, this includes: websites, bandwidth usage, protocols</strong></li>
	<li><strong>You will not access sites containing explicit or inappropriate material</strong></li>
	<li><strong>You will not attempt to access any system on this network</strong></li>
</ul>
<p>
{if $user_url}<span><a href="{$user_url}">If you are already logged in, continue to your site '{$user_url}'</a></span>{/if}


</p>
<div>
    Login Form
    <form method="post" action="nojslogin.php" class="generalForm"><!-- TODO: Make this submit over SSL --!>
    
        <div>
            <label for='username'>Username</label>
            <input type="text" name="username"/>
            <span id="UsernameInfo">Enter your username</span>
        </div>
        <div>
            <label for='password'>Password</label>
            <input type="text" name="password" />
            <span id='PasswordInfo'>Enter your password</span>
            
        </div>    
            
        <input type="hidden" name="userurl" value="{$user_url}"/>
        <input type="hidden" name="challenge" value="{$challenge}"/>        
        <button type="submit" name="submit">Login</button>        
    </form>
</div>
<div style="clear: left; clear: right">&nbsp;</div>




{include file="footer.tpl"}
