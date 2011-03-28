{include file="header.tpl" Name="Welcome" activepage="portal"}

<div id="page">
<h1>{$Location} Hotspot - Welcome</h1>

<p>Welcome to the {$Location} Hotspot. Please read the following before logging in.</p>
<p><a href="?help">Information and Help</a></p>

<p>For payment and an account, please contact the Office during office hours</p>
<p>By clicking the below link to login, you agree to the following:</p>
<ul>
	<li><strong>All your Internet activity will be monitored, including all websites visited, pages viewed, and traffic used</strong></li>
	<li><strong>You will not access sites containing explicit or inappropriate material</strong></li>
</ul>
<p>
<span id="loginlink"><a class="positive" href="nojs.php" target="Login_iServe" onclick="loginwindow = window.open('{$loginlink2}', 'Login_iServe', 'width=300,height=400,location=no,directories=no,status=yes,menubar=no,toolbar=no');loginwindow.moveTo(100,100);return false;">Login</a></span><br/>
{if $user_url}<span><a href="{$user_url}">If you are already logged in, continue to your site '{$user_url}'</a></span>{/if}


</p>
<div style="width: 45%; float: left">
	{include file="laptop_req.tpl"}
</div>
<div style="width: 45%; float: right">
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
