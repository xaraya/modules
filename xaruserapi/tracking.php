<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_tracking($args)
{
    extract($args);

    $tracking = array();

    // only track logged in users
    if (xarUserIsLoggedIn()) {
        // calling function should supply current time, if not, we set it here
        if (empty($now) || !is_numeric($now)) $now = time();
        // get the current expire setting (how long a user must have been
        // away before we consider this a new visit) default is 15 minutes
        $expire = xarModGetVar('crispbb', 'expirevisit');
        if (empty($expire) || !is_numeric($expire)) $expire = 15;
        // get the currently stored tracking for this user
        // each GUI function of the module updates this on every page view
        // where this api function is called.
        $string = xarModGetUserVar('crispbb', 'tracking');
        $tracking = !empty($string) && is_string($string) ? unserialize($string) : array();
        // added a timeonline tracker in v0.3.6
        if (empty($tracking[0]['timeonline'])) $tracking[0]['timeonline'] = 0;
        // if this is a user we've seen before thisvisit will be set
        // thisvisit is in fact the users last visit here
        if (!empty($tracking['0']['thisvisit'])) {
            // we see how long since lastvisit by subtracting it from the current time
            $interval = $now - $tracking[0]['thisvisit'];
            // see if it's been longer than the current expire setting since last visit
            if ($interval > ($expire*60)) {
                // if it has, this is a new visit
                // update the users last visit time
                $tracking['0']['lastvisit'] = $tracking[0]['thisvisit'];
                // make a note of the time this visit started
                $tracking['0']['visitstart'] = $now;
            // this is a current visit
            } else {
                // add the last interval to total time online (added in v.0.3.6)
                $tracking['0']['timeonline'] += $interval;
            }
        // this must be auser we've not seen before, either a new user, or module first run
        } else {
            // either way, we set sensible defaults
            $interval = 0;
            $tracking['0']['lastvisit'] = $now;
            $tracking['0']['visitstart'] = $now;
            $tracking['0']['timeonline'] = 0;
        }
        // TODO: deprecated, this should always be present now
        if (empty($tracking['0']['visitstart'])) {
            $tracking['0']['visitstart'] = $tracking['0']['lastvisit'];
        }
        // we set this visit on every page view
        $tracking['0']['thisvisit'] = $now;
        // add users time online for display (added in v.0.3.6)
        $tracking['0']['totalvisit'] = $now - $tracking['0']['timeonline'];
        // finally, we get the forum tracking values
        // this array stores last update times for each of the forums
        $fstring = xarModGetVar('crispbb', 'ftracking');
        $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
        // inject last update times into this users settings
        // this is used by calling function to determine unread items for this user
        foreach ($ftracking as $fid => $lastupdate) {
            $tracking[$fid][0]['lastupdate'] = $lastupdate;
        }
    }

    return $tracking;

}
?>