"use strict";
/* Chill Controller code is mostly
 *   Copyright (C) Y.Deltroo 2007
 *   Distributed under the BSD License
 *
 *   This file also contains third party code :
 *   - MD5, distributed under the BSD license
 *     http://pajhome.org.uk/crypt/md5
 *
 */

var challenge = 0;

var ident = '00';

var logintype = "";

// Setup "chilliController"

var chilliController = {
    interval: 30,
    host: "###SERVERIPADDRESS###",
    port: 3990,
    ident: '00',
    ssl: false,
    uamService: ''
};
chilliController.stateCodes = {
    UNKNOWN: -1,
    NOT_AUTH: 0,
    AUTH: 1,
    AUTH_PENDING: 2,
    AUTH_SPLASH: 3
};
chilliController.clientState = chilliController.stateCodes.UNKNOWN;

//var urlRoot = 'http://' + window.location.hostname + '/json/'; // TODO make this dynamic
var urlRoot = 'http://' + chilliController.host + ':' + chilliController.port + '/json/'; // TODO make this dynamic

chilliController.formatTime = function (t, zeroReturn)
{

    if (typeof (t) == 'undefined')
    {
        return "Not available";
    }

    t = parseInt(t, 10);
    if ((typeof (zeroReturn) != 'undefined') && (t === 0))
    {
        return zeroReturn;
    }
    if ((typeof (zeroReturn) != 'undefined') && (t < 0))
    {
        return zeroReturn;
    }

    var h = Math.floor(t / 3600);
    var m = Math.floor((t - 3600 * h) / 60);
    var s = t % 60;

    var s_str = s.toString();
    if (s < 10)
    {
        s_str = '0' + s_str;
    }

    var m_str = m.toString();
    if (m < 10)
    {
        m_str = '0' + m_str;
    }

    var h_str = h.toString();
    if (h < 10)
    {
        h_str = '0' + h_str;
    }

    if (t < 60)
    {
        return s_str + 's';
    }
    else if (t < 3600)
    {
        return m_str + 'm ' + s_str + 's';
    }
    else
    {
        return h_str + 'h ' + m_str + 'm ' + s_str + 's';
    }

};

chilliController.formatBytes = function (b, zeroReturn)
{

    if (typeof (b) == 'undefined')
    {
        b = 0;
    }
    else
    {
        b = parseInt(b, 10);
    }

    if ((typeof (zeroReturn) != 'undefined') && (b === 0))
    {
        return zeroReturn;
    }

    var kb = Math.round(b / 10.24) / 100;
    if (kb < 1) return b + ' b';

    var mb = Math.round(kb / 10.24) / 100;
    if (mb < 1) return kb + ' Kb';

    var gb = Math.round(mb / 10.24) / 100;
    if (gb < 1) return mb + ' Mb';

    return gb + ' Gb';
};

/* END Chilli Controller Code */

function display_error(errormsg)
{
    display_loginform();
    error_message(errormsg, 'alert-danger');

}

function tos_getresponse()
{
    // Send Challenge to automac script which will give us the response to send
    // and the username (so we never know the password clientside)

    /* Build automac URL */
    var tosUrl = 'http://' + window.location.hostname + '/grase/uam/automac?challenge=' + escape(challenge);

    chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;

    $.ajax(
    {
        url: tosUrl,
        dataType: "jsonp",
        timeout: 1000,
        jsonpCallback: "tos_get_login",
        error: function()
            {
                display_error("No response from TOS server");
            }
    });

}




function get_challenge()
{

    if (typeof (challenge) != 'string')
    {
        $.ajax(
        {
            url: urlRoot + 'status?callback=?',
            dataType: "jsonp",
            timeout: 5000,
            success: function (resp)
            {
                // Check for valid challenge

                if (typeof (resp.challenge) != 'string')
                {
                    display_error('Unable to get secure challenge');
                    return false;
                }
                if (resp.clientState === chilliController.stateCodes.AUTH)
                {
                    display_loggedinform();
                    error_message('Already logged in. Aborting login attempt');

                    return false;
                }
                // Check clientState

                /// ...

                // Got valid challenge and not logged in
                challenge = json.challenge;

                get_login();

            },
            error: function()
            {
                display_error("Server Timed Out. Please try again");
            }

        });
    }
    else
    {
        get_login();
    }
}

function get_login()
{
    // Redirect to the TOS login functions when it's a TOS login
    if (logintype == "TOS")
    {
        tos_getresponse();
        return false;
    }
    /* Calculate MD5 CHAP at the client side */
    var myMD5 = new ChilliMD5();

    var password = $("#password").val();
    var username = $("#username").val();

    if (typeof (password) !== 'string' || typeof (username) !== 'string' || password.length == 0 || username.length == 0)
    {
        display_error("Both username and password are needed");
        return false;
    }

    var chappassword = myMD5.chap(ident, password, challenge);

    /* Build /logon command URL */
    var logonUrl = urlRoot + 'logon?username=' + escape(username) + '&response=' + chappassword;

    chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;

    $.ajax(
    {
        url: logonUrl,
        dataType: "jsonp",
        timeout: 1000,
        jsonpCallback: "process_reply",
        error: function()
        {
            display_error("Login Failed due to server error. Please try again");
        }
    });

}


function tos_get_login(resp)
{
    if (typeof (resp) == 'undefined' || typeof (resp.username) !== 'string' || typeof (resp.response) !== 'string')
    {
        display_error("Incorrect response from TOS server. Please notify system admin");
        return false;
    }

    /* Build /logon command URL */
    var logonUrl = urlRoot + 'logon?username=' + escape(resp.username) + '&response=' + resp.response;

    chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;

    $.ajax(
    {
        url: logonUrl,
        dataType: "jsonp",
        timeout: 1000,
        jsonpCallback: "process_reply",
        error: function()
        {
            display_error("TOS login failed due to server error. Please try again");
        }
    });

}

var timeoutvar = 0;

function process_reply(resp)
{
    // Clear any previous timeout we have running
    clearTimeout(timeoutvar);

    //alert(resp);
    // Check for message (error)
    if (typeof (resp.message) == 'string')
    {
        error_message(resp.message);
    }

    if (typeof (resp.challenge) == 'string')
    {
        challenge = resp.challenge;
    }

    //client state
    if ((resp.clientState === chilliController.stateCodes.NOT_AUTH) ||
        (resp.clientState === chilliController.stateCodes.AUTH) ||
        (resp.clientState === chilliController.stateCodes.AUTH_SPLASH) ||
        (resp.clientState === chilliController.stateCodes.AUTH_PENDING))
    {

        if (resp.clientState === chilliController.stateCodes.NOT_AUTH)
        {

            display_loginform();
            chilliController.clientState = chilliController.stateCodes.NOT_AUTH;

        }

        if (resp.clientState === chilliController.stateCodes.AUTH)
        {
            if (chilliController.clientState === chilliController.stateCodes.AUTH_PENDING)
            {
                // We have sucessfully logged in or changed states to logged in
                error_message("Login successful", 'alert-success');
                var userurl = getQueryVariable('userurl');
                if (typeof (userurl) == 'string')
                {
                    userurl = decodeURIComponent(userurl)
                    error_message("Continue to your site <a target='_blank' href='" + userurl + "'>" + userurl + "</a>", 'alert-success');
                }

            }
            //console.log(chilliController.clientState);
            //console.log(resp.clientState);
            //console.log(chilliController.stateCodes.AUTH);
            chilliController.clientState = chilliController.stateCodes.AUTH;

            display_loggedinform();
            //$('#loggedinusername').text('Logged in as ' + resp.session.userName);
            $('#sessionstarttime').text('Since ' + chilliController.formatTime(resp.session.startTime));
            //$('#sessionTimeout').text('Session will end at ' + chilliController.formatTime(resp.session.sessionTimeout - resp.accounting.sessionTime));

            //if(resp.session.maxTotalOctets != undefined)
            //$('#sessionMaxTotalOctets').text(chilliController.formatBytes(resp.session.maxTotalOctets));
            $.each(resp.session, function (index, value)
            {
                switch (index)
                {
                case 'maxTotalOctets':
                    $('#sessionMaxTotalOctets').show();
                    $('#sessionMaxTotalOctetsVal').text(chilliController.formatBytes(value - resp.accounting.inputOctets - resp.accounting.outputOctets));
                    break;

                case 'sessionTimeout':
                    $('#sessionTimeout').show();
                    $('#sessionTimeoutVal').text(chilliController.formatTime(value - resp.accounting.sessionTime));
                    break;
                case 'userName':
                    $('#loggedinuserName').show();
                    $('#loggedinuserNameVal').text(value);

                }
            });

        }

        if (resp.clientState === chilliController.stateCodes.AUTH_PENDING)
        {
            chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;
            display_loadingform();
        }

    }
    else
    {
        display_error("Unknown clientState found in JSON reply");
    }

    // Clear any previous timeout we have running
    timeoutvar = setTimeout('update_status()', 10000);
}

function update_status()
{
    // Clear any previous timeout we have running
    clearTimeout(timeoutvar);

    $.ajax(
    {
        url: urlRoot + 'status',
        dataType: "jsonp",
        timeout: 1000,
        jsonpCallback: "process_reply"
    });
}

function logoff()
{
    $.ajax(
    {
        url: urlRoot + 'logoff',
        dataType: "jsonp",
        timeout: 1000,
        jsonpCallback: "process_reply",
        error: function()
        {
            display_error("Failed to logoff. Please try again");
        }
    });
}

function display_loginform()
{
    $('.alert-success').hide();
    $('#loginform').show();
    $('#tosaccept').show();
    $('#loading').hide();
    $('#loggedin').hide();
    logintype = "";
}

function display_loadingform()
{
    $('#loginform').hide();
    $('#tosaccept').hide();
    $('#loading').show();
    $('#loggedin').hide();
}

function display_loggedinform()
{
    $('#loginform').hide();
    $('#tosaccept').hide();
    $('#loading').hide();
    $('#loggedin').show();
    // We don't want to save the password, even if it's a nice feature
    $("#password").val('');
}

function error_message(msg, type)
{
    type = type || "";
    $("#errormessages").append('<div class="alert alert-dismissable ' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + msg + '</div>');
}

function clear_error_messages()
{
    $("#errormessages").html('');
}

// Setup our forms and action links

$('#loginform').submit(function ()
{
    logintype = "USER";
    display_loadingform();
    clear_error_messages()
    get_challenge();
    return false;
});

$('#tosaccept').submit(function ()
{
    logintype = "TOS";
    display_loadingform();
    clear_error_messages()
    get_challenge();
    return false;
});


$('#logofflink').click(function ()
{
    confirm("Are you sure you want to disconnect now?") && logoff();
    return false;
});

// Setup status window link

$('#statuslink').click(function ()
{
    var loginwindow = window.open('/grase/uam/mini', 'grase_uam', 'width=300,height=400,status=yes,resizable=yes');
    if (loginwindow)
    {
        loginwindow.moveTo(100, 100);
        loginwindow.focus();
    }
});

// Fire off our status updater
update_status();
