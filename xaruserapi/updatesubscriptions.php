<?php
/**
* Update subscription data
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
 * Update subscription data
 *
 * @author the eBulletin module development team
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_userapi_updatesubscriptions($args)
{
    extract($args);

    // get defaults
    if (empty($email)) $email = '';
    if (empty($name)) $name = '';
    if (empty($subscriptions)) $subscriptions = array();

    // process vars
    $loggedin = xarUserIsLoggedIn();
    if ($loggedin) {
        $uid = xarUserGetVar('uid');
        $email = '';
        $name = '';
    } else {
        $uid = '';
    }

    // validate vars
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*'
        . '@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (!empty($subscriptions) && !is_array($subscriptions)) {
        $invalid[] = 'subscriptions';
    }
    if (!$loggedin) {
        if (!preg_match($email_regexp, $email)) {
            $invalid[] = 'email';
        }
        if (empty($name)) {
            $invalid[] = 'name';
        }
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'updatesubscriptions', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // prepare for databas
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];

    // delete prior subscriptions for this user
    // (note: this also deletes subscriptions to hidden publications,
    //  so the hidden ones must also be passed if those subscriptions
    //  are to be preserved.)
    $bindvars = array();
    $query = "
        DELETE FROM $substable
        WHERE 1
    ";
    if ($loggedin) {
        $query .= "AND xar_uid = ?";
        $bindvars[] = $uid;
    } else {
        $query .= "AND xar_email = ?";
        $bindvars[] = $email;
    }
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // if no new subscriptions, we're done
    if ($subscriptions) {

        // now insert new values into table
        $query = "
            INSERT INTO $substable (
                xar_pid, xar_name, xar_email, xar_uid)
            VALUES
        ";
        $queries = $bindvars = array();
        foreach ($subscriptions as $pid => $value) {
            $queries[] = "(?,?,?,?)";
            $bindvars[] = $pid;
            $bindvars[] = $name;
            $bindvars[] = $email;
            $bindvars[] = $uid;
        }
        $query .= join(', ', $queries);

        $result = $dbconn->Execute($query, $bindvars);
        if (!$result) return;
    }

    // success
    return true;
}

?>
