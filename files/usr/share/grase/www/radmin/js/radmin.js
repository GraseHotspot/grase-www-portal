$(document).ready(function(){

    $("#DeleteUserConfirm").bind('paste', function(e) {
        alert("Please don't paste, just type the words exactly\nYes, I want to delete this user");
        return false;
    });

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
    $(".jsadd").addClass("ui-icon ui-icon-plus");
    $(".jsadd").unbind('click');
    $(".jsadd").click(function(){
        //$('<span class="jsremove"></span>').insertBefore(this)
        optiondiv = $(this).parent().clone(true);
        optiondiv.children('input').val('');
        $(this).parent().after(optiondiv);
        $(this).removeClass("jsadd").addClass("jsremove").unbind('click');
        //.val('');
        jsremove();
    
    });	
    
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
        $(this).parent().remove();
    
    });            
    $(".jsremove").addClass("ui-icon ui-icon-minus").removeClass("ui-icon-plus");
    //console.log($(".jsremove"));
}


