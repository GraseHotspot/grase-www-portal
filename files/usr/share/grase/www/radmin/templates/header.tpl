<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{$Title} - {$Name}</title>
<meta name="generator" content="{$Application} {$application_version}" />
<!-- CSS Stylesheet -->
<link rel="stylesheet" type="text/css" href="../hotspot.css" id="hotspot_css" />
<link rel="stylesheet" type="text/css" href="radmin.css?{$css_version}" id="radmin_css" />

<link type="text/css" href="css/cupertino/jquery-ui-1.7.2.custom.css" rel="stylesheet" />       

<link rel="shortcut icon" href="/grase/favicon.ico" />

<!-- jquery needed for custom functionality, must be loaded before prototype -->
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<!--<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>-->

<!-- Script for password strength checking -->
<script type="text/javascript" src="js/pwd_strength.js"></script>

<!-- / CSS Stylesheet -->
<link rel="shortcut icon" href="/favicon.ico" />


{literal}<script type="text/javascript"><!--//--><![CDATA[//><!--
function switchMenu(obj) {
	var el = document.getElementById(obj + '_body');
	var header = document.getElementById(obj + '_header');
	if(el != null){
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
		header.style.backgroundImage = 'url(images/plus.gif)';
	}
	else {
		el.style.display = '';
		header.style.backgroundImage = 'url(images/minus.gif)';
	}}
}
window.onload = function () {
	switchMenu("Machine"); /* Need to make this get constant from php */
/*	switchMenu("Ministry");
	switchMenu("Visitors");
	switchMenu("Staff");
	switchMenu("Students");*/
}

<!-- Copyright 2006,2007 Bontrager Connection, LLC
// http://bontragerconnection.com/ and http://www.willmaster.com/
// Version: July 28, 2007
var cX = 0; var cY = 0; var rX = 0; var rY = 0;
function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;}
function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;}
if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; }
else { document.onmousemove = UpdateCursorPosition; }
function AssignPosition(d) {
if(self.pageYOffset) {
	rX = self.pageXOffset;
	rY = self.pageYOffset;
	}
else if(document.documentElement && document.documentElement.scrollTop) {
	rX = document.documentElement.scrollLeft;
	rY = document.documentElement.scrollTop;
	}
else if(document.body) {
	rX = document.body.scrollLeft;
	rY = document.body.scrollTop;
	}
if(document.all) {
	cX += rX; 
	cY += rY;
	}
d.style.left = (cX+10) + "px";
d.style.top = (cY+10) + "px";
}
function HideContent(d) {
if(d.length < 1) { return; }
document.getElementById(d).style.display = "none";
}
function ShowContent(d,content) {
if(d.length < 1) { return; }
var dd = document.getElementById(d);
dd.innerHTML=content + "<br/><a onclick=\"HideContent('helpbox');\">[close]<\/a>";
AssignPosition(dd);
dd.style.display = "block";
}
function ReverseContentDisplay(d) {
if(d.length < 1) { return; }
var dd = document.getElementById(d);
AssignPosition(dd);
if(dd.style.display == "none") { dd.style.display = "block"; }
else { dd.style.display = "none"; }
}



// $.noConflict();
var $j = jQuery;


$j(document).ready(function(){
    $j(".datacost_item").click(function() {
        //alert($j(this).attr("title"));
        //alert($j("#MaxMb").val());
        $j("#MaxMb").val($j(this).attr("title"));
    });
    
    $j(".timecost_item").click(function() {
        //alert($j(this).attr("title"));
        //alert($j("#MaxTime").val());
        $j("#MaxTime").val($j(this).attr("title"));
    });
    
    
    /*
        1.) Form Field Value Swap
    */

    swapValues = [];    
    $j(".default_swap").each(function(i){
        swapValues[i] = $j(this).attr("title");
        if ($j.trim($j(this).val()) == "") {
            $j(this).val(swapValues[i]);
        }
        
        $j(this).focus(function(){
            if ($j(this).val() == swapValues[i]) {
                $j(this).val("");
            }
        }).blur(function(){
            if ($j.trim($j(this).val()) == "") {
                $j(this).val(swapValues[i]);
            }
        });
    });
    
    
});

$j(document).ready(function(){
    jQuery(function($){
        //$(".stripeMe tr").mouseover(function() {$(this).addClass("over");}).mouseout(function() {$(this).removeClass("over");});
        //$(".stripeMe tr:even").addClass("alt");
        $(".stripeMe").tablesorter(); // {sortList: [[0,0], [1,0]]});


        $("#userslistTable").tablesorter(); 

        $(".printlink").attr({ target: "_print"});
    });

}) ;




//--><!]]></script>{/literal}




</head>
<body>
{literal}<!--[if lte IE 6]><script src="/grase/ie6/warning2.js"></script><![endif]-->{/literal}
<div id="topbar">
<h1>{$Title} (v{$application_version})</h1>
{if ! $hidemenubar}{if $activepage != "login" && $activepage != "setup" && $activepage != "error"}{include file="menubar.tpl" }{/if}{/if}
</div>
<div id="helpbox" onclick="HideContent('helpbox');" style="display:none;">&nbsp;</div>
<div id="radminPage">
	<div id="messagebox" style="display: {if $messagebox}block;{else}none;{/if}">
	{foreach from=$messagebox item=msg}{$msg}<br/>{/foreach}
	</div>

