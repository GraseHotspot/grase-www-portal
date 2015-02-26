<?php

/* Auto MAC users allows us to automatically create users based on MAC address
 * with a different password to computer accounts, in a group specifically for
 * them. It's main use case will be to allow you to give "Free" access to users
 * based on device, but still with limits, and still allowing them to be
 * overridden with a voucher. I.e. anyone can have 30 minutes free a day.
 */

/* Basic structure
 *
 * From login screen (so someone not logged in), give them an Automatic login 
 * url that is basically an I Agree to TOS button[1]
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
 * */

namespace Grase;

class autoCreateUser
{

    private $settings;
    private $databaseFunctions;
    // TODO replace _GET with request
    private $request;

    public function __constructor(\Grase\Database\Radmin $Settings, DatabaseFunctions $databaseFunctions)
    {
        $this->settings = $Settings;
        $this->databaseFunctions = $databaseFunctions;

    }


    public function automacuser($json = false)
    {
        // TODO MAC is passed in via uam
        $mac = $this->databaseFunctions->latestMacFromIP(\Grase\Util::remoteIP());
        $autoUsername = $this->mactoautousername($mac);

        // Attempt to create user
        //
        $autoCreateGroup = $this->settings->getSetting('autocreategroup');
        $autoCreatePassword = $this->settings->getSetting('autocreatepassword');
        $groupSettings = $this->settings->getGroup($autoCreateGroup);
        /* TODO Set at the group level and not in the radcheck table,
         * requires changes to how DB class works
         */

        if ($autoCreateGroup && strlen($autoUsername) > 0) {
            // Create user
            $this->databaseFunctions->createUser(
                $autoUsername,
                $autoCreatePassword,
                false, // Data limit
                false, // Time limit
                '--', // Expiry date
                $groupSettings[$autoCreateGroup]['ExpireAfter'],
                $autoCreateGroup,
                "Auto created account for $mac at " . date('Ymd H:i:s')
            );

            // Users password may not match the autocreatepassword if it's changed.
            // Should we update the users password or get the users password?
            $this->databaseFunctions->setUserPassword(
                $autoUsername,
                $autoCreatePassword
            );

            // Create CHAP Challenge/Response token
            $challenge = $_GET['challenge'];
            $response = $this->chapchallengeresponse($challenge, $autoCreatePassword);

            $loginURL = $this->uamloginurl($autoUsername, $response);

            if ($json) {
                return json_encode(array('username' => $autoUsername, 'challenge' => $challenge, 'response' => $response));
            } else {
                header("Location: $loginURL");
                return false;
            }
        }
        return false;
    }

    private function chapchallengeresponse($challenge, $password)
    {
        // Generates a response for a challenge response CHAP Auth
        $hexChallenge = pack("H32", $challenge);
        $response = md5("\0" . $password . $hexChallenge);

        return $response;
    }

    private function uamloginurl($username, $response)
    {
        $networkoptions = unserialize($this->settings->getSetting("networkoptions"));
        $lanIP = $networkoptions['lanipaddress'];

        $username = urlencode($username);
        $response = urlencode($response);
        return "http://$lanIP:3990/login?username=$username&response=$response";
    }

        public function mactoautousername($mac)
        {
        // Check it's a MAC
        //
        // Turn it into a reversible username but isn't at first glace a mac
        // address?

        // Strip : and - from address, lowercase it, reverse it
        $autoUsername = strrev(strtolower(str_replace(array(":", "-"), "", $mac)));

        return $autoUsername;
        }
}
