<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{$Title} - {$Name}</title>
<meta name="generator" content="{$Application} {$application_version}" />
<!-- CSS Stylesheet -->
<link rel="stylesheet" type="text/css" href="/grase/hotspot.css" id="hotspot_css" />
<link rel="stylesheet" type="text/css" href="radmin.css?{$css_version}" id="radmin_css" />



<!--<link type="text/css" href="css/cupertino/jquery-ui-1.7.2.custom.css" rel="stylesheet" />       -->
	<link type="text/css" href="/javascript/jquery-ui/themes/base/jquery.ui.all.css" rel="stylesheet" />	
	<link type="text/css" href="/grase/css/cupertino/jquery-ui-1.8.11.custom.css" rel="stylesheet" />	

    <script language="javascript" type="text/javascript" src="/javascript/jquery/jquery.js"></script>

    <script language="javascript" type="text/javascript" src="/javascript/jquery-ui/jquery-ui.js"></script>
    
    <script language="javascript" type="text/javascript" src="/grase/js/jquery.tablesorter.min.js"></script>    
    
    <script language="javascript" type="text/javascript" src="/grase/js/grase.js"></script>        

<link rel="shortcut icon" href="/grase/favicon.ico" />

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



        
        $(".printlink").attr({ target: "_print"});
    });

}) ;

$(document).ready(function(){

    
    $.tablesorter.defaults.widgets = ['zebra']; 
    
    $("#userslistTable").tablesorter({
        sortForce: [[1,0]], 
        sortList: [[0,0],[1,0]] 
    });     
    $("#myTable2").tablesorter(); 
    
    $('.dialog').dialog({
			autoOpen: false,
			modal: true
		});  



}) ;


//--><!]]></script>{/literal}

</head>
<body>
<div id="page">
    {literal}<!--[if lte IE 6]><script src="/grase/ie6/warning2.js"></script><![endif]-->{/literal}
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

