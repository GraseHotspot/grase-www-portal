<!DOCTYPE html>
<html>
<head>
<title>{$logintitle}{if $Name} - {t}{$Name}{/t}{/if}</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<meta name="generator" content="GRASE - UAM" />
<!-- CSS Stylesheet -->
{if !$disableallcss}
    <link rel="stylesheet" type="text/css" href="/grase/hotspot.css" id="hotspot_css" />
    <link rel="stylesheet" type="text/css" href="/grase/radmin/radmin.css" id="radmin_css" />
	<link type="text/css" href="/grase/css/cupertino/jquery-ui-1.8.11.custom.css" rel="stylesheet" />
{/if}
<style type="text/css">
{$tpl_maincss}
</style>
<!-- / CSS Stylesheet -->
<!-- Favicon -->
<link rel="shortcut icon" href="/grase/favicon.ico" />
    <script type="text/javascript" src="/grase/js/jquery/jquery-1.5.2.min.js"></script>
    <script type="text/javascript" src="/grase/js/jquery/jquery-ui-1.8.11.custom.min.js"></script>    
    <script type="text/javascript" src="/grase/js/grase.js"></script>        
 
{if $activepage == "nojsstatus"}	
    <meta http-equiv="refresh" content="60">
{/if}
</head>
<body>
{if $activepage != "mini"}{literal}<!--[if lte IE 6]><script src="/grase/ie6/warning2.js"></script><![endif]-->{/literal}{/if}
