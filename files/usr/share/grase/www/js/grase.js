$(document).ready(function(){

    $("button").button(); // Turn buttons into buttons
    
    $('.autoDisable').attr('autocomplete','off'); // Turn off autocomplete based on class autoDisable
    
    $("#DeleteUserConfirm").bind('paste', function(e) {
        alert("Please don't paste, just type the words exactly\nYes, I want to delete this user");
        return false;
    });

    // Simple jQuery to create help dialogs out of all elements with class helpbutton and a title attribute		
	$('.helpbutton[title]').each(function(index, element){
	    content = $(element).attr('title');
	    $(element).attr('title', '');
	    createHelpDialog('helpdialog' + index, 'Help', content);
	    $(element).click(function()
	    {
	        $('#helpdialog'+index).dialog('open');
	    })
	});
	

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

