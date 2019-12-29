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

namespace App\Util;

use App\Entity\Radius\Group;
use App\Entity\Radius\RadPostAuth;
use App\Entity\Radius\User;
use App\Entity\Setting;
use App\Entity\UpdateUserData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class contains the logic to automatically create a user account for a device (based on the MAC address)
 * This is used for the "free login", also known as the TOS login (just accept the Terms of Service to use the connection)
 */
class AutoCreateUser
{
    /**
     * Create the auto login user, calculate the response to the CHAP challenge to avoid exposing the password
     *
     * @param SettingsUtils          $settings
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return array
     */
    public function autoMacUser(SettingsUtils $settings, Request $request, EntityManagerInterface $em)
    {
        // MAC is passed in via uam, but it may still be best to get the IP from here (prevents spoofing requests)
        $mac = $this->latestMacFromIP($request->getClientIp(), $em);
        $autoUsername = $this->macToAutoUsername($mac);

        // Attempt to create user
        //
        $autoCreateGroupName = $settings->getSettingValue(Setting::AUTO_CREATE_GROUP);
        $autoCreateGroup = $em->getRepository(Group::class)->findBy(['name' => $autoCreateGroupName]);
        $autoCreatePassword = $settings->getSettingValue(Setting::AUTO_CREATE_PASSWORD);

        if ($autoCreateGroup && strlen($autoUsername) > 0) {
            // Try and find the user so we can update or create them
            $user = $em->getRepository(User::class)->find($autoUsername);
            $newUser = false;
            if (!$user) {
                $user = new User();
                $newUser = true;
            }

            $newUserData = UpdateUserData::fromUser($user);

            if ($newUser) {
                $newUserData->comment = "Auto created account for $mac at " . date('Ymd H:i:s');
                $newUserData->username = $autoUsername;
            }

            // Always ensure we have the correct password and group for autologin users
            $newUserData->password = $autoCreatePassword;
            $newUserData->primaryGroup = $autoCreateGroup;

            // TODO ExpireAfter?? Or we can just process that now and set the Expiry?
            $newUserData->updateUser($user, $em);

            // Create CHAP Challenge/Response token
            $challenge = $_GET['challenge'];
            $response = $this->chapChallengeResponse($challenge, $autoCreatePassword);

            return ['success' => true, 'username' => $autoUsername, 'challenge' => $challenge, 'response' => $response];
        }

        return ['success' => false];
    }


    /**
     * This is a simple function that will to a MAC address, strip out the - and : chars, ensure
     * it's all lower case, then reverse it. That gives us something that isn't obviously the
     * users MAC address at first glace, but is easily reversed to turn it back into the MAC address
     *
     * @param $mac
     *
     * @return string
     */
    public function macToAutoUsername($mac)
    {
        // @TODO Check it's a MAC

        // Strip : and - from address, lowercase it, reverse it
        return strrev(strtolower(str_replace([":", "-"], "", $mac)));
    }

    /**
     * Get the clients MAC address from a recent login attempt (for auto mac login)
     * We can move this to a helper function in the future so it can be shared.
     *
     * @param $ipAddress string IP Address of the client we want to find the MAC address for
     * @param $em        EntityManagerInterface
     *
     * @return bool|RadPostAuth
     */
    private function latestMacFromIP($ipAddress, EntityManagerInterface $em)
    {
        // We limit the selection to a machine that has connected in the last
        // hour, (this may need to be updated in the future with
        // CallingStationId for multiple APs)
        /** @var RadPostAuth $lastPostAuth */
        $lastPostAuth = $em->createQueryBuilder()
            ->select('r')
            ->from(RadPostAuth::class, 'r')
            ->andWhere('r.framedIpAddress = :framedIpAddress')
            ->setParameter('framedIpAddress', $ipAddress)
            ->andWhere('r.username LIKE \'__-__-__-__-__-__\'')
            ->andWhere('r.authDate > :oneHourAgo')
            ->setParameter('oneHourAgo', new \DateTime('now -1 hour'))
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        if (!$lastPostAuth) {
            return false;
        }

        // @TODO check this is a MAC address, verify it against the CallingStationID?
        return $lastPostAuth->getUsername();
    }

    /**
     * Calculate the CHAP challenge/response response
     * @param $challenge
     * @param $password
     *
     * @return string
     */
    private function chapChallengeResponse($challenge, $password)
    {
        // Generates a response for a challenge response CHAP Auth
        $hexChallenge = pack("H32", $challenge);
        $response = md5("\0" . $password . $hexChallenge);

        return $response;
    }
}
