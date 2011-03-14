{include file="header.tpl" Name="Edit User" activepage="user"}
<script type="text/javascript" src="js/pwd_strength.js"></script>
<div id="edituserForm">
<h2>My Details</h2>

{if $user.Group eq MACHINE_GROUP_NAME}
<div class="errorPage" style="display: block;"> <span id="errorMessage">Machine Account Locked (Only Admin can edit){if $error}<br/>{foreach from=$error item=msg}{$msg}<br/>{/foreach}{/if}</span> </div>
<table>
	<tr><td>Username</td><td>{$user.Username}</td></tr>
	<tr><td>Group</td><td>{$user.Group}</td></tr>
	<tr><td>Data Limit (Mb)</td><td>{$user.MaxMb}</td></tr>
	<tr><td>Expiry</td><td>{$user.Expiration}</td></tr>
</table>

{else}

<div class="errorPage" style="display: {if $error}block;{else}none;{/if}"><span id="errorMessage">{foreach from=$error item=msg}{$msg}<br/>{/foreach}</span> </div>
<form method='post'>
<table>
	<tr><td>Username</td><td>{$user.Username}</td></tr>
	<tr><td>New Password</td><td> <input id="newpassword" type="password" name="NewPassword" value='' onkeyup="runPassword(this.value, 'newpassword');"/>
	                        <div style="width: 200px;float: right;"> 
                                <div id="newpassword_text" ></div>
                                <div id="newpassword_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></div> 
                        </div>

	</td></tr>
	<tr><td>Confirm Password</td><td> <input type="password" name="PasswordVerify" value=''/><button type="submit" name="changepasswordsubmit" value="Change Password"><img src="/grase/images/icons/textfield_key.png" alt=""/>Change Password</button></form></td></tr>
	<tr><td>Group</td><td>{$user.Group}</td></tr>
	
	<tr><td>Data Limit (Mb)</td><td>{$user.MaxMb}</td></tr>
	<tr><td>Time Limit (Mins)</td><td>{$user.MaxTime}</td></tr>

	<tr><td>Expiry (Automatic)</td><td>{$user.FormatExpiration}</td></tr>
	<tr>&nbsp;</tr>
</table>
</div>
{/if}


{include file="footer.tpl"}
