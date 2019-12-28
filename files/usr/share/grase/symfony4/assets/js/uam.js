const $ = require('jquery');
require('./chilliMD5');
require('bootstrap');
require('admin-lte');


let chilliController = {
    stateCodes: {
        UNKNOWN: -1,
        NOT_AUTH: 0,
        AUTH: 1,
        AUTH_PENDING: 2,
        AUTH_SPLASH: 3
    },
    clientState: -1,
    challenge: null,
    loginType: null,
    timeoutvar: null,
    ident: '00',
};

// TODO make this dynamic (it could be the uam server, or it could be a different server, depending on the setup)
chilliController.urlRoot = 'http://' + window.location.hostname + ':' + 3990 + '/json/';

chilliController.formatTime = function (t, zeroReturn) {

    if (typeof (t) == 'undefined') {
        return "Not available";
    }

    t = parseInt(t, 10);
    if ((typeof (zeroReturn) != 'undefined') && (t === 0)) {
        return zeroReturn;
    }
    if ((typeof (zeroReturn) != 'undefined') && (t < 0)) {
        return zeroReturn;
    }

    const h = Math.floor(t / 3600);
    const m = Math.floor((t - 3600 * h) / 60);
    const s = t % 60;

    let s_str = s.toString();
    if (s < 10) {
        s_str = '0' + s_str;
    }

    let m_str = m.toString();
    if (m < 10) {
        m_str = '0' + m_str;
    }

    let h_str = h.toString();
    if (h < 10) {
        h_str = '0' + h_str;
    }

    if (t < 60) {
        return s_str + 's';
    } else if (t < 3600) {
        return m_str + 'm ' + s_str + 's';
    } else {
        return h_str + 'h ' + m_str + 'm ' + s_str + 's';
    }

};

chilliController.formatBytes = function (b, zeroReturn) {

    if (typeof (b) == 'undefined') {
        b = 0;
    } else {
        b = parseInt(b, 10);
    }

    if ((typeof (zeroReturn) != 'undefined') && (b === 0)) {
        return zeroReturn;
    }

    const kb = Math.round(b / 10.24) / 100;
    if (kb < 1) return b + ' b';

    const mb = Math.round(kb / 10.24) / 100;
    if (mb < 1) return kb + ' Kb';

    const gb = Math.round(mb / 10.24) / 100;
    if (gb < 1) return mb + ' Mb';

    return gb + ' Gb';
};


chilliController.getChallenge = function () {
    if (typeof (self.challenge) != 'string') {
        $.ajax(
            {
                url: chilliController.urlRoot + 'status?callback=?',
                dataType: "jsonp",
                timeout: 5000,
                success: function (resp) {
                    // Check for valid challenge

                    if (typeof (resp.challenge) != 'string') {
                        clearErrorMessages();
                        display_error('Unable to get secure challenge');
                        return false;
                    }
                    if (resp.clientState === chilliController.stateCodes.AUTH) {
                        pageStates.loggedInFormState();
                        clearErrorMessages();
                        error_message('Already logged in. Aborting login attempt');

                        return false;
                    }
                    // Check clientState

                    /// ...

                    // Got valid challenge and not logged in
                    chilliController.challenge = resp.challenge;

                    chilliController.getLogin();

                },
                error: function () {
                    clearErrorMessages();
                    display_error("Server Timed Out.<br/>Please try again");
                }

            });
    } else {
        chilliController.getLogin();
    }
}

chilliController.getLogin = function () {
    // Redirect to the TOS login functions when it's a TOS login
    if (chilliController.loginType === "TOS") {
        chilliController.tosGetResponse();
        return false;
    }
    /* Calculate MD5 CHAP at the client side */
    const myMD5 = new ChilliMD5();

    const password = $("#password").val();
    const username = $("#username").val();

    if (typeof (password) !== 'string' || typeof (username) !== 'string' || password.length === 0 || username.length === 0) {
        display_error("Both username and password are needed");
        return false;
    }

    const chappassword = myMD5.chap(chilliController.ident, password, chilliController.challenge);

    /* Build /logon command URL */
    const logonUrl = chilliController.urlRoot + 'logon?username=' + encodeURIComponent(username) + '&response=' + encodeURIComponent(chappassword);

    chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;

    $.ajax(
        {
            url: logonUrl,
            dataType: "jsonp",
            timeout: 5000,
            jsonpCallback: chilliController.processReply.name,
            error: function () {
                clearErrorMessages();
                display_error("Login Failed due to server error. Please try again");
            }
        });
}

chilliController.tosGetResponse = function () {
    // Send Challenge to automac script which will give us the response to send
    // and the username (so we never know the password client side)

    // TODO get this from the router (grase_uam_toslogin)
    /* Build automac URL */
    const tosUrl = 'http://' + window.location.hostname + '/grase/uam/automac?challenge=' + encodeURIComponent(chilliController.challenge);


    chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;

    $.ajax(
        {
            url: tosUrl,
            dataType: "jsonp",
            timeout: 5000,
            jsonpCallback: chilliController.tosGetLogin.name,
            error: function () {
                clearErrorMessages();
                display_error("No response from TOS server");
            }
        });
}

chilliController.tosGetLogin = function (resp) {
    // Check for an invalid response
    if (typeof (resp) == 'undefined' || typeof (resp.success !== 'boolean')) {
        display_error("Incorrect response from TOS server. Please notify system admin");
        return false;
    }

    /*
     * Check if the response was success or failure. The check of username and response are a bit redundant, they'll
     * be missing if success is false, and they should be valid strings if success is true. It's still a good idea to
     * check them though
     */
    if (!resp.success || typeof (resp.username) !== 'string' || typeof (resp.response) !== 'string') {
        display_error("An error occurred trying to login. Please notify the system admin")
        return false
    }

    /* Build /logon command URL */
    const logonUrl = chilliController.urlRoot + 'logon?username=' + encodeURIComponent(resp.username) + '&response=' + encodeURIComponent(resp.response);

    chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;

    $.ajax(
        {
            url: logonUrl,
            dataType: "jsonp",
            timeout: 5000,
            jsonpCallback: chilliController.processReply.name,
            error: function () {
                clearErrorMessages();
                display_error("TOS login failed due to server error. Please try again");
            }
        });
}

chilliController.processReply = function (resp) {
    // Clear any previous timeout we have running
    clearTimeout(chilliController.timeoutvar);

    //alert(resp);
    // Check for message (error)
    if (typeof (resp.message) == 'string') {
        error_message(resp.message);
    }

    if (typeof (resp.challenge) == 'string') {
        chilliController.challenge = resp.challenge;
    }

    //client state
    if ((resp.clientState === chilliController.stateCodes.NOT_AUTH) ||
        (resp.clientState === chilliController.stateCodes.AUTH) ||
        (resp.clientState === chilliController.stateCodes.AUTH_SPLASH) ||
        (resp.clientState === chilliController.stateCodes.AUTH_PENDING)) {

        if (resp.clientState === chilliController.stateCodes.NOT_AUTH) {

            pageStates.loginFormState()
            chilliController.clientState = chilliController.stateCodes.NOT_AUTH;

        }

        if (resp.clientState === chilliController.stateCodes.AUTH) {
            if (chilliController.clientState === chilliController.stateCodes.AUTH_PENDING) {
                // We have successfully logged in or changed states to logged in
                error_message("Login successful", 'alert-success');
                let userUrl = getQueryVariable('userurl');
                if (typeof (userUrl) == 'string') {
                    userUrl = decodeURIComponent(userUrl);
                    error_message("Continue to your site <a target='_blank' href='" + userUrl + "'>" + userUrl + "</a>", 'alert-success');
                }

            }
            chilliController.clientState = chilliController.stateCodes.AUTH;

            pageStates.loggedInFormState();
            //$('#loggedinusername').text('Logged in as ' + resp.session.userName);
            $('#sessionstarttime').text('Since ' + chilliController.formatTime(resp.session.startTime));
            //$('#sessionTimeout').text('Session will end at ' + chilliController.formatTime(resp.session.sessionTimeout - resp.accounting.sessionTime));

            $.each(resp.session, function (index, value) {
                switch (index) {
                    case 'maxTotalOctets':
                        $('#sessionMaxTotalOctets').show();
                        // TODO Gigawords in resp.accounting
                        $('#sessionMaxTotalOctetsVal').text(chilliController.formatBytes(value - resp.accounting.inputOctets - resp.accounting.outputOctets));
                        break;

                    case 'sessionTimeout':
                        $('#sessionTimeout').show();
                        $('#sessionTimeoutVal').text(chilliController.formatTime(value - resp.accounting.sessionTime));
                        break;
                    case 'userName':
                        $('#loggedinuserName').show();
                        $('#loggedinuserNameVal').text(value);
                        break
                }
            });

            /*$.each(resp.accounting, function (index, value)
             {
             updateStatusPage(index, value);
             });*/

        }

        if (resp.clientState === chilliController.stateCodes.AUTH_PENDING) {
            chilliController.clientState = chilliController.stateCodes.AUTH_PENDING;
            pageStates.loadingFormState();
        }

    } else {
        display_error("Unknown clientState found in JSON reply");
    }

    // Clear any previous timeout we have running
    chilliController.timeoutvar = setTimeout(chilliController.updateStatus, 10000);
}

chilliController.updateStatus = function () {
    // Clear any previous timeout we have running
    clearTimeout(chilliController.timeoutvar);

    $.ajax(
        {
            url: chilliController.urlRoot + 'status',
            dataType: "jsonp",
            timeout: 5000,
            jsonpCallback: chilliController.processReply.name
        });
}

chilliController.logoff = function () {
    $.ajax(
        {
            url: chilliController.urlRoot + 'logoff',
            dataType: "jsonp",
            timeout: 5000,
            jsonpCallback: chilliController.processReply.name,
            error: function () {
                display_error("Failed to logoff. Please try again");
            }
        });
}

chilliController.startLogin = function (event, type) {
    chilliController.logintype = type;
    pageStates.loadingFormState();
    clearErrorMessages();
    chilliController.challenge = null;
    chilliController.getChallenge();
    return false;
}

const pageStates = {
    loginFormState: function () {
        $('.alert-success').hide();
        $('#loginform').show();
        $('#tosaccept').show();
        $('#loading').hide();
        $('#loggedin').hide();
        $('#loggedin>span').hide();
        chilliController.logintype = "";
        console.log("Loading login form state");
    },
    loadingFormState: function () {
        $('#loginform').hide();
        $('#tosaccept').hide();
        $('#loading').show();
        $('#loggedin').hide();
        console.log("Loading loading form state");
    },
    loggedInFormState: function () {
        $('#loginform').hide();
        $('#tosaccept').hide();
        $('#loading').hide();
        $('#loggedin').show();
        // We don't want to save the password, even if it's a nice feature
        $("#password").val('');
        console.log("Loading logged in form state");
    }

}


function display_error(errormsg) {
    pageStates.loginFormState()
    error_message(errormsg, 'alert-danger');

}

function error_message(msg, type) {
    type = type || "";
    $("#errormessages").append(
        '<div class="alert alert-dismissible ' + type + ' fade show" role="alert">'
        + msg +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
        + '</div>'
    );
}

function clearErrorMessages() {
    $("#errormessages").html('');
}

// Setup our forms and action links

$('#loginform').submit((event) => chilliController.startLogin(event, "USER"));

$('#tosaccept').submit((event) => chilliController.startLogin(event,"TOS"));


$('#logofflink').click(function () {
    confirm("Are you sure you want to disconnect now?") && chilliController.logoff();
    return false;
});

// Setup status window link

// TODO we've not yet written the new mini window
$('#statuslink').click(function () {
    const loginwindow = window.open('/grase/uam/mini', 'grase_uam', 'width=300,height=400,status=yes,resizable=yes');
    if (loginwindow) {
        loginwindow.moveTo(100, 100);
        loginwindow.focus();
    }
});

// Fire off our status updater
chilliController.updateStatus();

global.chilliController = chilliController;