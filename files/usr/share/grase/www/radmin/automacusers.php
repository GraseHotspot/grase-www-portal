<?php

// Auto MAC users allows us to automatically create users based on MAC 
// address with a different password to computer accounts, in a group 
// specifically for them. It's main use case will be to allow you to give 
// "Free" access to users based on device, but still with limits, and still 
// allowing them to be overridden with a voucher. I.e. anyone can have 30 mins 
// free a day.
//

/* Basic structure
 *
 * From login screen (so someone not logged in), give them an Automatic login 
 * url that is basically an I Aggree to TOS button[1]
 * At automatic login. Get their MAC address. Turn it into our funky automatic 
 * login username, and check if it already exists. If it doesn't exist, create 
 * it in the special group for automatic created accounts[2]. We can actually 
 * skip the check and just try and create it
 * Then create the login URL that includes the chap challenge response (so 
 * users never need to see the password) and redirect them to it (the login URL 
 * that uses chilli). We can modify this system so if it's an AJAX request, we 
 * can pass it directly back and let the javascript side do it, but for now 
 * it'll be a HTTP redirect and then non-javascript login system is used?
 *
 * This way, if they have used up all their time, at login they'll get the 
 * message
 *
 *
 * [1] This can be a more advanced form that collects data, but that's probably 
 * better for the already written voucher registration system?
 * [2] Need a method for expiry that doesn't lock them out, but allows them to 
 * be deleted after being inactive for a period so they don't "spam" the system
 *
 *
 * */

function automacuser($json = false)
{
    global $Settings;
    // TODO MAC is passed in via uam
    $mac = DatabaseFunctions::getInstance()->latestMacFromIP(remoteip());
    $autousername = mactoautousername($mac);

    // Attempt to create user
    //
    $autocreategroup = $Settings->getSetting('autocreategroup');
    $autocreatepassword = $Settings->getSetting('autocreatepassword');

    if($autocreategroup)
    {
        // Create user
        DatabaseFunctions::getInstance()->createUser(
            $autousername,
            $autocreatepassword,
            false, // Data limit
            false, // Time limit
            '--', // Expiry date
            $autocreategroup,
            "Auto created account for $mac at ". date('Ymd H:i:s')
        );

        // Create CHAP Challenge/Response token
        $challenge = $_GET['challenge'];
        $response = chapchallengeresponse($challenge, $autocreatepassword);

        $loginurl = uamloginurl($autousername, $response);

        if($json)
        {
            return json_encode(array('username' => $autousername, 'challenge' => $challenge, 'response' => $response));
        }else{
            header("Location: $loginurl");
            return false;
        }
    }

    // Login redirect
}

function chapchallengeresponse($challenge, $password)
{
    // Generates a response for a challenge response CHAP Auth
    $hexchal = pack ("H32", $challenge);
    $response = md5("\0" . $password . $hexchal);

    return $response;
}

function uamloginurl($username, $response)
{
    global $lanip;
    $username = urlencode($username);
    $response = urlencode($response);
    return "http://$lanip:3990/login?username=$username&response=$response";
}

function mactoautousername($mac)
{
    // Check it's a MAC
    //
    // Turn it into a reversible username but isn't at first glace a mac 
    // address?

    // Strip : and - from address, lowercase it, reverse it
    $autousername = strrev(strtolower(str_replace(array(":", "-"), "", $mac)));

    return $autousername;
}

/* TODO: Check where this code came from */
    /* TODO move to central location as this is now used twice at least*/
     function remoteip()
     {
        if (getenv('HTTP_CLIENT_IP'))
        {
           $ip = getenv('HTTP_CLIENT_IP');
        }
        elseif (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_X_FORWARDED'))
        {
            $ip = getenv('HTTP_X_FORWARDED');
        }
        elseif (getenv('HTTP_FORWARDED_FOR'))
        {
            $ip = getenv('HTTP_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_FORWARDED'))
        {
            $ip = getenv('HTTP_FORWARDED');
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
	}
?>
