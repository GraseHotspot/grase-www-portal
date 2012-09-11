// JS function for correct sorting of table numbers 
var TableTextExtraction = function(node)  
{  
    // extract data from markup and return it  
    if(node.title != "")
    {
        return node.title;

    }
    if(node.textContent == '')
    {
        return '-1';
    }
    return node.textContent; 
}  

$(document).ready(function(){

/*    $("#DeleteUserConfirm").bind('paste', function(e) {
        alert("Please don't paste, just type the words exactly\nYes, I want to delete this user");
        return false;
    });*/

    // Simple jQuery to create help dialogs out of all elements with class helpbutton and a title attribute		
	$('.helpbutton[title]').each(function(index, element){
	    content = $(element).attr('title');
	    //$(element).attr('title', '');
	    createHelpDialog('helpdialog' + index, 'Help', content);
	    $(element).click(function()
	    {
	        $('#helpdialog'+index).dialog('open');
	    })
	});
	
	$('.helptext').each(function(index, element){
	    content = $(element).html();
	    createLargeHelpDialog('helpdialog_' + index, 'Help', content);
	    $(element).click(function()
        {
            $('#helpdialog_'+index).dialog('open');
        });
        $(element).html('');
        $(element).addClass("ui-icon ui-icon-info");
	});
	
	// Remove help text that is for those not using JS
	
    $(".nojshelp").remove();
    
    // Make multi remove elements remoable
    jsremove();

    // Make multi element adder function
    $(".jsaddbutton").addClass("ui-icon ui-icon-plus");
    $(".jsadd").unbind('click');
    $(".jsadd").click(function(){
        //$('<span class="jsremove"></span>').insertBefore(this)
        optiondiv = $(this).parent().clone(true);
        optiondiv.children('input').val('');
        $(this).parent().after(optiondiv);
        $(this).children('.jsaddbutton').removeClass("jsaddbuton").addClass("jsremovebutton");
        $(this).children('.jsaddremovetext').text($("#jsdeletetext").attr('title'));
        $(this).removeClass("jsadd").addClass("jsremove").unbind('click');
        //.val('');
        jsremove();
    
    });	
    
    $(".jsadd").show();
    $(".jsremove").show();
    
    
    // Multi element add/remove for tables
    // Make multi remove elements remoable
    tablejsremove();

    // Make multi element adder function
    $(".jsaddtablebutton").addClass("ui-icon ui-icon-plus");
    $(".jsaddtable").unbind('click');
    $(".jsaddtable").click(function(){
        optiontr = $(this).closest('tr').clone(true);
        optiontr.find('input').val('').attr('checked', false);;
        $(this).parent().parent().after(optiontr);
        $(this).find('.jsaddtablebutton').removeClass("jsaddtablebuton").addClass("jsremovetablebutton");
        $(this).find('.jsaddremovetext').text($("#jsdeletetabletext").attr('title'));
        $(this).removeClass("jsaddtable").addClass("jsremovetable").unbind('click');
        //.val('');
        tablejsremove();
    
    });	
    
    $(".jsaddtable").show();
    $(".jsremovetable").show();    
    
    
    // Make tables of users into tabbed display
    $("#userslist").tabs();
    
    // Make group list tabbed
    $("#groupSettingsNewGroup").hide();
    var tab_counter = 1;
    
    var $grouptabs = $("#groupslist").tabs({
        tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>"/*,
	add: function( event, ui ) {
	        var tab_content = "Tab content.";
	        //$( ui.panel ).append( $("#groupSettings_NewGroup").html() );
	}*/
    });
        
        // Add each tab        
    $("div[id*=groupSettings_]").each(function(index) {
            $("#groupslist").tabs("add", "#" + $(this).attr('id'), $(this).children('.groupnameinput').val() );
    });
    
        // Make close buttons on tabs work
        //TODO migrate live to .on event
        $( "#groupslist span.ui-icon-close" ).live( "click", function() {
			var index = $( "li", $grouptabs ).index( $( this ).parent() );
			$grouptabs.tabs( "remove", index );
		});
	
	
	function addTab() {
	        $("#groupSettingsNewGroup").clone().attr('id','groupSettingsDyn_' + tab_counter).show().appendTo("#groupslist");
	        //$("#tabs-2").html($("#groupSettings_NewGroup").html());
                $grouptabs.tabs( "add", "#groupSettingsDyn_" + tab_counter, "New Group" );		

		tab_counter++;
	        $(".groupnameinput").change( function() {
                $('a[href=#' + $(this).parent().attr("id") + ']').text($(this).val());
                });
	}

	$( "#addgroup" ).button()
	               .click(function() {
	                        addTab();
	                        return false;
	               }
	               );

        // Update tab names on name Update
        $(".groupnameinput").change( function() {
                $('a[href=#' + $(this).parent().attr("id") + ']').text($(this).val());
        });
        
        /*TODO update to 1.7 and replace above with below
                $("#groupslist").on("change", ".groupnameinput", function() {
                $('a[href=#' + $(this).parent().attr("id") + ']').text($(this).val());
        });*/





    
    // Group properties in edit user collapsed
	$('.collapseheader').click(function() {
		$(this).next().toggle('slide' , { direction: "up" });
		return false;
	}).button({
            icons: {
                secondary: "ui-icon-triangle-1-s"
            }
        }).next().hide();    
    
    
    // Stuff for displaying text in empty field until there is content
    $(".datacost_item").click(function() {
        $("#MaxMb").val($(this).attr("title"));
    });
    
    $(".timecost_item").click(function() {
        $("#MaxTime").val($(this).attr("title"));
    });    
    
    /*
        1.) Form Field Value Swap
    */

    swapValues = [];    
    $(".default_swap").each(function(i){
        swapValues[i] = $(this).attr("title");
        if ($.trim($(this).val()) == "") {
            $(this).val(swapValues[i]);
        }
        
        $(this).focus(function(){
            if ($(this).val() == swapValues[i]) {
                $(this).val("");
            }
        }).blur(function(){
            if ($.trim($(this).val()) == "") {
                $(this).val(swapValues[i]);
            }
        });
    });    
    
    // Sort and stripe tables 
    $.tablesorter.defaults.widgets = ['zebra']; 
    
    $(".stripeMe tbody tr").mouseover(function() {$(this).addClass("ui-state-highlight");}).mouseout(function() {$(this).removeClass("ui-state-highlight");});
    //$(".stripeMe tr:even").addClass("alt");
            
    $(".stripeMe").tablesorter({
        textExtraction: TableTextExtraction
    }); // {sortList: [[0,0], [1,0]]});

    // Sidebar menu collapse/expander for submenu items
    
        
        function collapse(submenuid, fast)
        {
            if(fast)
            {
                $('#'+submenuid).hide();            
            }
            else
            {
            $('#'+submenuid).hide('slideUp');
            }
            $('#'+submenuid).prevAll('.expand').show();
            $('#'+submenuid).prevAll('.collapse').hide();           
            $.cookie(submenuid, 'collapsed');            
        }
        
        function expand(submenuid)
        {
            $('#'+submenuid).show('slideDown');
            $('#'+submenuid).prevAll('.expand').hide();
            $('#'+submenuid).prevAll('.collapse').show();
            $.cookie(submenuid, 'expanded');           
        }        
        
        $(".collapse").click(function() {
            collapse($(this).nextAll('.submenu').attr('id'));
        });
        
        $(".expand").click(function() {
            expand($(this).nextAll('.submenu').attr('id'));
        });
        
        $(".submenu").each(function(){
            thisid = $(this).attr('id');
            cookievalue = $.cookie(thisid);
            if(cookievalue == 'collapsed'){
                collapse(thisid, 1);
             
            }else{
                expand(thisid);            
            }
        });
        
        $(".topmenu").click(function(){
            expand('submenu'+$(this).attr('id'));
        });
        
        expand($(".ui-state-active").parent('.submenu').attr('id'));    
    
    
    // Create sidebar hider/shower
    $('#sidebartoggle').remove(); // Make sure it doesn't exist (odd bug somwhere)
    $('#menucontainer').before('\
<div id="sidebartoggle" class="ui-state-default">\
<span id="sidebartogglebutton" class="ui-icon ui-icon-arrowthick-2-e-w">&nbsp;</span><span id="sidebartoggletext"></span>\
<span id="sidebartogglebutton2" class="ui-icon ui-icon-arrowthick-2-e-w">&nbsp;</span>\
</div>\
');
    //$('#sidebartoggletext').easyRotate({ degrees: -90 });

    $('#sidebar').css('width','13em');
    $('#pagecontent').css('marginLeft','14em');


     $('#sidebartoggle').toggle(
           function()
           {
                $('#menucontainer').animate({width: 'toggle'});
                $('#sidebar').animate({width:20});
                $('#pagecontent').animate({marginLeft:20});
                // Using background image instead of text as transform not supported well enough
                $('#sidebartoggletext').css("background-image", "url(/grase/radmin/images/show.png");

           },
           function()
           {
                $('#menucontainer').animate({width: 'toggle'});
                $('#sidebar').animate({width:'13em'});
                $('#pagecontent').animate({marginLeft:'14em'});
//                $('#sidebartoggletext').text("Hide&nbsp;Menu");
                $('#sidebartoggletext').css("background-image", "url(/grase/radmin/images/hide.png");
                
           });
    
    // Setup dialog box for help messages
    $('.dialog').dialog({
	        autoOpen: false,
	        modal: true
        });  
        
    // Table filter for admin table

    var theTable = $('#AdminlogTable')

    $("#filter").keyup(function() {
        $.uiTableFilter( theTable, this.value );
    })

    $('#filter-form').submit(function(){
        return false;
    }).focus(); //Give focus to input field


}) ;

function createHelpDialog(id, title, content)
{
    // Simple function that uses jQuery UI to create a (Closed) dialog box for later use
    $("<div/>", {
        id: id,
        html: content
    }).dialog({
        autoOpen: false,
        title: title,
        resizable: false,        
    })
}

function createLargeHelpDialog(id, title, content)
{
    // Simple function that uses jQuery UI to create a (Closed) dialog box for later use
    $("<div/>", {
        id: id,
        html: content
    }).dialog({
        autoOpen: false,
        title: title,
        resizable: true,  
        width: 450,
        show: 'fade',
        hide: 'fade',
    })
}


// JS Function for removing a multi form element (sets up click handler)
function jsremove(){
    $(".jsremove").click(function(){
        //$(this).prev().remove();
        //$(this).next().remove();            
        //$(this).remove();
        confirm("Delete item?") &&            // TODO: Translation
        $(this).parent().remove();
    
    });            
    $(".jsremovebutton").addClass("ui-icon ui-icon-minus").removeClass("ui-icon-plus");
    //console.log($(".jsremove"));
}

function tablejsremove(){
    $(".jsremovetable").click(function(){
        //$(this).prev().remove();
        //$(this).next().remove();            
        //$(this).remove();
        confirm("Delete item?") &&            // TODO: Translation
        $(this).parent().parent().remove();
    
    });            
    $(".jsremovetablebutton").addClass("ui-icon ui-icon-minus").removeClass("ui-icon-plus");
    //console.log($(".jsremove"));
}


