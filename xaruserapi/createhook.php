<?php
/**
* Add default subscriptions when a user registers
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
 * Add default subscriptions when a user registers
 *
 * @author the eBulletin module development team
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_userapi_createhook($args)
{
    extract($args);

    // validate arsg
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'user', 'createhook', 'eBulletin');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // set defaults
    if (!isset($extrainfo) || !is_array($extrainfo)) $extrainfo = array();

    // get module name
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }

    // get module ID
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'userapi', 'createhook', 'eBulletin');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // get itemtype
    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    // don't subscribe groups, and don't let anyone use this hook except roles module
    if ($modname != 'roles' || $itemtype != 0) {
        return $extrainfo;
    }

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];

    // get public publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('public' => true));
    if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // if we have no pubs, return
    if (empty($pubs)) return $extrainfo;

    // coming from roles module, objectid is the uid
    $uid = $objectid;

    // now insert new values into table
    $query = "INSERT INTO $substable (xar_pid, xar_uid) VALUES ";
    $queries = $bindvars = array();
    foreach ($pubs as $pid => $pub) {
        $queries[] = "(?,?)";
        $bindvars[] = $pid;
        $bindvars[] = $uid;
    }
    $query .= join(', ', $queries);

    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return $extrainfo;

    // success
    return $extrainfo;
}

?>
