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

    // set defaults
    if (empty($stype)) $stype = 'reg';

    // validate vars
    $invalid = array();
    if (empty($pid) || !is_numeric($pid)) {
        $invalid[] = 'pid';
    }
    if (empty($stype) ||
        !is_string($stype) ||
        ($stype != 'reg' && $stype != 'non')) {
        $invalid[] = 'subscription type';
    }
    if (!empty($names) &&
        ($stype == 'reg' && !is_array($names)) ||
        ($stype == 'non' && !is_string($names))
    ) {
        $invalid[] = 'names';
    }
    // throw error if bad data
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'createsubscribers', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // if no names given, just return
    if (empty($names)) {
        return array();
    }

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$pid")) return;

    // initialize lists
    $msgs = array();
    $subs = array();

    // we need this for both subscription types!
    $roles = new xarRoles();

    switch($stype) {
    case 'non':

        // trim off all delineators and split into lines
        $names = trim(preg_replace("/[\>\<\,\;\" ]+/", ' ', $names));
        $lines = preg_split("/\s*(\r\n|\n\r|\n|\r)+\s*/", $names);

        // prepare to validate emails
        $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@'
            . '([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';

        // process lines one by one
        foreach ($lines as $line) {

            $line = trim($line);

            // assume everything after the last space is email
            $offset = strrpos($line, ' ');

            // validate offset
            if (!$offset) {
                $msgs[] = array('error', xarML('No name given.'), $line);
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

            // check if this user is actually registered
            $user = $roles->_lookuprole('xar_email', $email);
            if ($user) {
                $msgs[] = array(
                    'error',
                    xarML('Registered User'),
                    xarML(
                        '#(1) &lt;#(2)&gt; is a registered user.  Please subscribe'
                            . ' this person through the Registered Users screen.',
                        $user->getName(),
                        $user->getEmail()
                    )
                );
                continue;
            }
            // validate email
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
            $subs[$email] = array($name, $email, '');
        }

        break;
    case 'reg':
    default:

        foreach ($names as $uid) {

            // make sure user exists
            if (!$roles->getRole($uid)) {
                $msgs[] = array(
                    'error',
                    xarML('Invalid User ID'),
                    xarML('User ID #(1).', $uid)
                );
                continue;
            }
            // look out for duplicates
            if (isset($subs[$uid])) {
                $msgs[] = array(
                    'warn',
                    xarML('Duplicate Entry'),
                    xarML('#(1) &lt;#(2)&gt;', $roles->getName(), $roles->getEmail()));
                continue;
            }
            // we made it!  put into array
            $subs[$uid] = array('', '', $uid);
        }
    }

    // return now if all names failed
    if (empty($subs)) return $msgs;

    // give errors for those who are already subscribed
    if ($stype == 'non') {

        // get subscribers
        $subscribed = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers_non',
            array('emails' => array_keys($subs), 'pid' => $pid)
        );
        if (empty($subscribed) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // scan list and give warnings as necessary
        foreach ($subs as $email => $sub) {
            foreach ($subscribed as $scr) {
                if ($scr['email'] == $email) {
                    $msgs[] = array(
                        'warn',
                        xarML('Already Subscribed'),
                        xarML('#(1) &lt;#(2)&gt;', $sub[0], $email)
                    );
                    unset($subs[$email]);
                    break;
                }
            }
        }
    } elseif ($stype == 'reg') {

        // get subscribers
        $subscribed = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers_reg',
            array('uids' => array_keys($subs), 'pid' => $pid)
        );
        if (empty($subscribed) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // scan list and give warnings as necessary
        foreach ($subs as $uid => $sub) {
            foreach ($subscribed as $scr) {
                if ($scr['uid'] == $uid) {
                    $msgs[] = array(
                        'warn',
                        xarML('Already Subscribed'),
                        xarML('#(1) &lt;#(2)&gt;', $scr['name'], $scr['email'])
                    );
                    unset($subs[$uid]);
                    break;
                }
            }
        }
    }

    /** the only subs remaining now are the ones we need to add. **/

    // return if nothing left
    if (empty($subs)) return $msgs;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];

    // generate query
    $query = "
        INSERT INTO $substable
            (xar_pid, xar_name, xar_email, xar_uid)
        VALUES
    ";
    $queryparts = array();
    $bindvars = array();
    foreach ($subs as $sub) {
        $queryparts[] = "(?,?,?,?)";
        $bindvars[] = $pid;
        $bindvars[] = $sub[0];
        $bindvars[] = $sub[1];
        $bindvars[] = $sub[2];
    }
    $query .= join(",\n", $queryparts);

    // insert new records
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // add success messages
    if ($stype == 'non') {
        foreach ($subs as $sub) {
            $msgs[] = array('success', 'Successfully subscribed', xarML('#(1) &lt;#(2)&gt;', $sub[0], $sub[1]));
        }
    } elseif ($stype == 'reg') {
        foreach ($subs as $sub) {
            $user = $roles->getRole($sub[2]);
            $msgs[] = array('success', 'Successfully subscribed', xarML('#(1) &lt;#(2)&gt;', $user->getName(), $user->getEmail()));
        }
    }

    // success
    return $msgs;
}

?>
