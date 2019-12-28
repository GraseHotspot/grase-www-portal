<!DOCTYPE html>
<html>
{* <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> *}
<head>
<title>{$Title} - {t}{$Name}{/t}</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<meta name="generator" content="{$Application} {$application_version}" />
{if $autorefresh}<meta http-equiv="refresh" content="{$autorefresh}">{/if}
<!-- CSS Stylesheet -->
<link rel="stylesheet" type="text/css" href="/grase/hotspot.css?{$hotspotcssversion}" id="hotspot_css" />
<link rel="stylesheet" type="text/css" href="radmin.css?{$radmincssversion}" id="radmin_css" />
    <link rel="stylesheet" href="/grase/vendor/fontawesome/css/font-awesome.min.css">

    <link type="text/css" href="/grase/css/cupertino/jquery-ui-1.8.11.custom.css" rel="stylesheet" />

    <script type="text/javascript" src="/grase/vendor/jquery/dist/jquery.min.js"></script>

    <script type="text/javascript" src="/grase/js/jquery/jquery-ui-1.8.11.custom.min.js"></script>
    
    <script type="text/javascript" src="/grase/vendor/jquery.tablesorter/js/jquery.tablesorter.min.js"></script>    
    <script type="text/javascript" src="/grase/js/jquery.uitablefilter.js"></script>        

    <script type="text/javascript" src="/grase/radmin/js/radmin.js?{$radminjsversion}"></script>    

    

<link rel="shortcut icon" href="/grase/favicon.ico" />

<!-- Script for password strength checking -->
<script type="text/javascript" src="js/pwd_strength.js"></script>

<!-- / CSS Stylesheet -->
<link rel="shortcut icon" href="/favicon.ico" />


{literal}<script type="text/javascript"><!--//--><![CDATA[//><!--

var $j = jQuery;

$j(document).ready(function(){
    jQuery(function($){
    
        $(".printlink").attr({ target: "_print"});

                
    });        

}) ;

//--><!]]></script>{/literal}

</head>
<body>
<div id="page">
    <div id="topbar">
        <h1>{$Title} (v{$application_version})</h1>
    </div>

    <!-- Sidebar (Menu) -->
    <div id="sidebar">
        {if ! $hidemenubar && $activepage != "login" && $activepage != "setup" && $activepage != "error"}{include file="menubar.tpl" }{/if}
        &nbsp;
    </div>

    
    <!-- Main content -->
    <div id="pagecontent">
        {include file="errors.tpl"}

