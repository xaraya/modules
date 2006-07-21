<?php
/**
 * File: $Id:
 *
 * Get all module items
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * get all maxercalls items
 *
 * @author the Maxercalls module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @param catid $ ID of the category
 * @returns array
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function maxercalls_userapi_getall($args)
{
    extract($args);
    if (!xarVarFetch('catid', 'int:1:', $catid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int::', $numitems, '-1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uid', 'int:1:', $uid, '', XARVAR_NOT_REQUIRED)) return;

    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ViewMaxercalls')) return;
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $maxercallstable = $xartable['maxercalls'];
    //$uid = xarUserGetVar('uid');

    $query = "SELECT xar_callid,
                   xar_calldate,
                   xar_calltime,
                   xar_calltext,
                   xar_owner,
                   xar_remarks,
                   xar_enterts,
                   xar_enteruid";
    // Add category
    if (xarModIsHooked('categories','maxercalls') && (!empty($catid))) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('maxercalls'),
                                             'catid' => $catid));
        $query .= "
                  FROM ( $maxercallstable
                  LEFT JOIN $categoriesdef[table]
                  ON $categoriesdef[field] = xar_callid )
                  $categoriesdef[more]
                  WHERE $categoriesdef[where]
                  AND ";
    } else {
        $query .= "
                  FROM $maxercallstable
                  WHERE ";
    }
    // Build admin option to retreive all
    if ((xarSecurityCheck('DeleteMaxercalls', 0)) && empty($uid)) {

        $query .= " xar_owner != 0  ";
    } else {
        $currentuid = xarUserGetVar('uid');
        $query .= "         xar_owner = $currentuid ";
    }

    // Order clause
    $query .="
              ORDER BY xar_calldate DESC ";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($callid, $calldate, $calltime, $calltext, $owner, $remarks, $enterts, $enteruid) = $result->fields;
        if (xarSecurityCheck('ViewMaxercalls', 0, 'Call', "$callid:All:$enteruid")) {
            $items[] = array('callid' => $callid,
                'calldate' => $calldate,
                'calltime' => $calltime,
                'calltext' => $calltext,
                'owner'    => $owner,
                'remarks'  => $remarks,
                'enterts'  => $enterts,
                'enteruid' => $enteruid);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>
