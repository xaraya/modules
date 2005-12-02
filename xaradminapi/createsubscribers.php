<?php
/**
* Create new subscribers
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Create new subscribers
 *
 */
function ebulletin_adminapi_createsubscribers($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    if (empty($pid) || !is_numeric($pid)) {
        $invalid[] = 'pid';
    }
    if (!empty($registered) && !is_array($registered)) {
        $invalid[] = 'registered users list';
    }
    if (!empty($unregistered) && !is_string($unregistered)) {
        $invalid[] = 'unregistered names list';
    }

    // throw error if bad data
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'createsubscribers', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    // set defaults
    if (empty($unregistered)) $unregistered = '';
    if (empty($registered)) $registered = array();

    // initialize lists
    $msgs = array();
    $subs = array();

    // prepare to scan for users
    $roles = new xarRoles();

    // parse unregistered string
    if (!empty($unregistered)) {

        // trim off all delineators and split into lines
        $unreg = preg_replace("/[\>\<\,\;\" ]+/", ' ', $unregistered);
        $unreg = trim($unreg);
        $lines = preg_split("/\s*(\r\n|\n\r|\n|\r)+\s*/", $unreg);

        // prepare to validate emails
        $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';

        // process lines one by one
        foreach ($lines as $line) {

            $line = trim($line);

            // assume everything after the last space is email
            $offset = strrpos($line, ' ');

            // validate offset
            if (!$offset) {
                $msgs[] = array('error', xarML('Not Valid'), $line);

                continue;
            }

            // get name and email
            $name = substr($line, 0, $offset);
            $email = strtolower(substr($line, $offset+1));

            // validate name and email
            if (!$name) {
                $msgs[] = array('error', xarML('No Name'), $line);
                continue;
            }

            // check if this email belongs to a registered user
            // TODO: find a Xar-sanctified way of locating a role without calling
            // a private function (only alternative right now seems to be
            // querying the xar_roles table directly)
            $user = $roles->_lookuprole('xar_email', $email);
            if ($user) {
                // don't send an error!  just put their UID into the
                // $registered list
                $registered[] = $user->getID();
                continue;
            }

            if (!$email || !preg_match($email_regexp, $email)) {
                $msgs[] = array('error', xarML('Invalid Email'), $line);
                continue;
            }
            // look out for duplicates
            if (isset($subs[$email])) {
                $msgs[] = array('warn', xarML('Duplicate Email'), $line);
                continue;
            }
            // we made it!  put into array
            $subs[$email] = array($pid, $name, $email);
        }

    }

    // add registered users
    if ($registered) {
        $roles = new xarRoles();
        foreach ($registered as $uid) {

            // make sure user exists
            if (!$roles->getRole($uid)) {
                $msgs[] = array('error', xarML('Invalid User ID'), xarML('User ID #(1).', $uid));
                continue;
            }
            // look out for duplicates
            if (isset($subs[$uid])) {
                $msgs[] = array('warn', xarML('Duplicate Entry'), xarML('#(1) &lt;#(2)&gt;', $roles->getName(), $roles->getEmail()));
                continue;
            }
            // we made it!  put into array
            $subs[$uid] = array($pid, '', $uid);
        }
    }

    // check who's already subscribed
    $subscribed = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers', array('emails' => array_keys($subs)));
    if (empty($subscribed) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    foreach ($subscribed as $sub) {
        $msgs[] = array('warn', xarML('Already Subscribed'), xarML('#(1) &lt;#(2)&gt;', $sub['name'], $sub['email']));
        if (!empty($sub['uid'])) {
            unset($subs[$sub['uid']]);
        } elseif (isset($subs[$sub['email']])) {
            unset($subs[$sub['email']]);
        }
    }

    // store into database if anything is left
    if (!empty($subs)) {
        // prepare for database
        $dbconn = xarDBGetConn();
        $xartable = xarDBGetTables();
        $substable = $xartable['ebulletin_subscriptions'];

        // generate query
        $query = "INSERT INTO $substable (xar_pid, xar_name, xar_email) VALUES ";
        $queryparts = array();
        $bindvars = array();
        foreach ($subs as $sub) {
            $queryparts[] = "(?,?,?)";
            $bindvars[] = $sub[0];
            $bindvars[] = $sub[1];
            $bindvars[] = $sub[2];
        }
        $query .= join(",\n", $queryparts);

        // insert new records
        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) return;
    }

    // record success messages
    foreach ($subs as $sub) {
        if (is_numeric($sub[2])) {
            $user = $roles->getRole($sub[2]);
            $msgs[] = array('success', 'Subscribed', xarML('#(1) &lt;#(2)&gt;', $user->getName(), $user->getEmail()));
        } else {
            $msgs[] = array('success', 'Subscribed', xarML('#(1) &lt;#(2)&gt;', $sub[1], $sub[2]));
        }
    }

    // success
    return $msgs;
}

?>
