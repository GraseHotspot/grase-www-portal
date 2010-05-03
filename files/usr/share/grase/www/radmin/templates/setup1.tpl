{include file="header.tpl" Name="Inital Setup" activepage="setup"}

<div id="setupForm">
<h2>Setup for {$Application}</h2>
<div id="errorPage" style="display: {if $error}block;{else}none;{/if}"> <span id="errorMessage">{$error}</span> </div>
<h3>Step 1 - Configuration Files</h3>

<div style="text-align: left; color: black;">
This page will take the default settings, allowing you to change them, and then generate 3 files, the site config, the administration password file, and the database config. You will need to manually create these files, and ensure the permissions of each file is correct for the operation of the {$Application}. There are examples of some of these files in the configs directory, so you can just copy those to the correct locations and edit them manually. To generate the administration password file it is best to use this setup script.
</div>
<div>
<form method='post'>

<table>

<tr id="config_file" class="setup_item"><td>Config File<br/>
<span class="item_description">The application configuration file (Name and location can not be changed).</span><br/>
<span class="error">WARNING: If you have already created this file yourself, and put in your own settings, if it is writeable by the webserver, it will be overwritten by this script.</span><br/>
{if $CONFIG}{if $config_file_error}<span class="good">* {$config_file_error}</span>{else}
<span class="good">* "{$config_file}" is writeable by the webserver. Please read the above warning.</span>{/if}{/if}</td>
<td>{$config_file}</td></tr>

<tr id="location_name" class="setup_item"><td>Location Name<br/>
<span class="item_description">Location Name for installation</span><br/>
{if $CONFIG}{if $location_name_error}<span class="error">* {$location_name_error}</span>{else}
<span class="good">* "{$location_name}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="location_name" value='{$location_name}'/></td></tr>

<tr id="admin_user_passwd_file" class="setup_item"><td>Admin Password File<br/>
<span class="item_description">This file contains all the usernames and passwords for the administration interface. It is best kept in a location outside of the webserver. For example '/home/user/private/admin_users'. (It will need to be writeable by the webserver if you wish to change the admin password or add more admin users later)</span><br/>
{if $CONFIG}{if $admin_user_passwd_file_error}<span class="warn">* {$admin_user_passwd_file_error}</span>{else}
<span class="good">* "{$admin_user_passwd_file}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="admin_user_passwd_file" value='{$admin_user_passwd_file}'/></td></tr>

<tr id="admin_user" class="setup_item"><td>Administrator User<br/>
<span class="item_description">Initial Administrator Username</span><br/>
{if $CONFIG}{if $admin_user_error}<span class="error">* {$admin_user_error}</span>{else}
<span class="good">* "{$admin_user}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="admin_user" value='{$admin_user}'/></td></tr>

<tr id="admin_user_passwd" class="setup_item"><td>Administrator Password<br/>
<span class="item_description">Initial Adminsitrator Password</span><br/>
{if $CONFIG}{if $admin_user_passwd_error}<span class="error">* {$admin_user_passwd_error}</span>{else}
<span class="good">* "{$admin_user_passwd}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="admin_user_passwd" value='{$admin_user_passwd}'/></td></tr>

<tr id="database_config_file" class="setup_item"><td>Database Config File<br/>
<span class="item_description">Database settings. Usually in /etc/radmin.conf</span><br/>
{if $CONFIG}{if $database_config_file_error}<span class="warn">* {$database_config_file_error}</span>{else}
<span class="good">* "{$database_config_file}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="database_config_file" value='{$database_config_file}'/></td></tr>

<tr id="sql_server" class="setup_item"><td>MySQL Server<br/>
<span class="item_description">Server name (hostname, usually localhost)</span><br/>
{if $CONFIG}{if $sql_server_error}<span class="error">* {$sql_server_error}</span>{else}
<span class="good">* "{$sql_server}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="sql_server" value='{$sql_server}'/></td></tr>

<tr id="sql_root_username" class="setup_item"><td>MySQL Server Poweruser<br/>
<span class="item_description">Username that will be used to create the database and user for this application (can be left blank)</span><br/>
{if $CONFIG}{if $sql_root_username_error}<span class="warn">* {$sql_root_username_error}</span>{else}
<span class="good">* "{$sql_root_username}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="sql_root_username" value='{$sql_root_username}'/></td></tr>

<tr id="sql_root_password" class="setup_item"><td>MySQL Server Poweruser Password<br/>
<span class="item_description">Password for above poweruser</span><br/>
{if $CONFIG && 0}{if $sql_root_password_error}<span class="error">* {$sql_root_password_error}</span>{else}
<span class="good">* "{$sql_root_password}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="sql_root_password" value='{$sql_root_password}'/></td></tr>


<tr id="sql_username" class="setup_item"><td>MySQL Server Username<br/>
<span class="item_description">Username that has been (will be) created just for this application</span><br/>
{if $CONFIG}{if $sql_username_error}<span class="error">* {$sql_username_error}</span>{else}
<span class="good">* "{$sql_username}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="sql_username" value='{$sql_username}'/></td></tr>

<tr id="sql_password" class="setup_item"><td>MySQL Server Password<br/>
<span class="item_description">Password that has been (will be) created just for this application</span><br/>
{if $CONFIG}{if $sql_password_error}<span class="error">* {$sql_password_error}</span>{else}
<span class="good">* "{$sql_password}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="sql_password" value='{$sql_password}'/></td></tr>

<tr id="sql_database" class="setup_item"><td>MySQL Server Database<br/>
<span class="item_description">Database that has been (will be) created just for this application</span><br/>
{if $CONFIG}{if $sql_database_error}<span class="error">* {$sql_database_error}</span>{else}
<span class="good">* "{$sql_database}" is valid.</span>{/if}{/if}</td>
<td><input type="text" name="sql_database" value='{$sql_database}'/></td></tr>

<tr></tr>
<tr id="buttons"><td></td><td><input type="submit" name="setup1submit" value="Generate Config Files"/></td></tr>

</table>
</form>

{if !$error && $CONFIG}
<div id="configs">
<p>Generating configuartion files...</p>
<p>Please copy these files to the correct location as specified. If the locations are wrong, please regenerate with the correct file locations above.</p>

{if $siteconfig}
<div class="configfile"><span class="description{if $siteconfig_file_written} file_written{/if}"><em>{$config_file}</em> {if $siteconfig_file_written}successfully created{else}needs to be manually created{/if}</span>
<pre class="configfile">{$siteconfig}</pre>
</div>{/if}


{if $admin_user_config}
<div class="configfile"><span class="description{if $admin_user_file_written} file_written{/if}"><em>{$admin_user_passwd_file}</em> {if $admin_user_file_written}successfully created{else}needs to be manually created{/if}</span>
<pre class="configfile">{$admin_user_config}</pre>
</div>{/if}

{if $database_config}
<div class="configfile"><span class="description{if $database_config_file_written} file_written{/if}"}><em>{$database_config_file}</em> {if $database_config_file_written}successfully created{else}needs to be manually created{/if}</span>
<pre class="configfile">{$database_config}</pre>
</div>{/if}

</div>{/if}

</div>

{include file="footer.tpl"}
