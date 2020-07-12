<!DOCTYPE html>
<html>
<head>
<title>{$logintitle}{if $Name} - {t}{$Name}{/t}{/if}</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<meta name="generator" content="GRASE - UAM" />
<meta name="referrer" content="origin">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSS Stylesheet -->
{if !$disableallcss}
<link rel="stylesheet" type="text/css" href="/grase/vendor/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/grase/vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/grase/uam/uam.css">
{/if}
<style type="text/css">
{$tpl_maincss}
</style>
<!-- / CSS Stylesheet -->
<!-- Favicon -->
<link rel="shortcut icon" href="/grase/favicon.ico" />
    <script type="text/javascript" src="/grase/vendor/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="/grase/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

{if $activepage == "nojsstatus"}
    <meta http-equiv="refresh" content="60">
{/if}
</head>
<body>
{if !$hidemenu }
    <nav class="navbar navbar-default navbar-static-top navbar-inverse" role="navigation">
        <div class="container-fluid" style="max-width: 960px">
            <div class="navbar-header">
{if $activepage != "mini"}
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                 <span class="sr-only">Toggle navigation</span>
                 <span class="icon-bar"></span>
                 <span class="icon-bar"></span>
                 <span class="icon-bar"></span>
                 </button>
{/if}
                 <a class="navbar-brand" href="#">{$logintitle}</a>
            </div>
{if $activepage != "mini"}

            <div class="collapse navbar-collapse" id="navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li {if $activepage == 'portal'}class="active"{/if}><a href="/grase/uam/hotspot">{t}Login{/t}</a></li>
<!--                <li><a href="#">Features</a></li>
                <li><a href="#">Contact</a></li>-->
                {if !$hidehelplink}<li {if $activepage == 'help'}class='active'{/if}><a href="help">{t}Help{/t}</a></li>{/if}
              </ul >
            </div>
{/if}
        </div>
    </nav>
{/if}
<div class="container">
