<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * rate an item
 *
 * @param $args['modname'] module name of the item to rate
 * @param $args['itemtype'] item type (optional)
 * @param $args['objectid'] ID of the item to rate
 * @param $args['rating'] actual rating
 * @return int the new rating for this item
 */
function ratings_userapi_rate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($objectid)) ||
        (!isset($rating) || !is_numeric($rating) || $rating < 0 || $rating > 100)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('value'), 'user', 'rate', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    xarML('module id'), 'user', 'rate', 'ratings');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    // Security Check
    if(!xarSecurityCheck('CommentRatings',1,'Item',"$modname:$itemtype:$objectid")) return;


    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ratingstable = $xartable['ratings'];

    // Multipe rate check
    if (!empty($itemtype)) {
        $seclevel = xarModGetVar('ratings', "seclevel.$modname.$itemtype");
        if (!isset($seclevel)) {
            $seclevel = xarModGetVar('ratings', 'seclevel.'.$modname);
        }
    } else {
        $seclevel = xarModGetVar('ratings', 'seclevel.'.$modname);
    }
    if (!isset($seclevel)) {
        $seclevel = xarModGetVar('ratings', 'seclevel');
    }
    if ($seclevel == 'high') {
        if (xarUserIsLoggedIn()) {
            $rated = xarModGetUserVar('ratings',$modname.':'.$itemtype.':'.$objectid);
            if (!empty($rated) && $rated > 1) {
                return;
            }
        } else {
            return;
        }
    } elseif ($seclevel == 'medium') {
        // Check to see if user has already voted
        if (xarUserIsLoggedIn()) {
            $rated = xarModGetUserVar('ratings',$modname.':'.$itemtype.':'.$objectid);
            if (!empty($rated) && $rated > time() - 24*60*60) {
                return;
            }
        } else {
            $rated = xarSessionGetVar('ratings:'.$modname.':'.$itemtype.':'.$objectid);
            if (!empty($rated) && $rated > time() - 24*60*60) {
                return;
            }
        }
    } // No check for low

    // Get current information on rating
    $query = "SELECT xar_rid,
                   xar_rating,
                   xar_numratings
            FROM $ratingstable
            WHERE xar_moduleid = ?
              AND xar_itemid = ?
              AND xar_itemtype = ?";
    $bindvars = array($modid, $objectid, $itemtype);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    if (!$result->EOF) {
        // Update current rating
        list($rid, $currating, $numratings) = $result->fields;
        $result->close();

        // Calculate new rating
        $newnumratings = $numratings + 1;
        $newrating = (int)((($currating*$numratings) + $rating)/$newnumratings);

        // Insert new rating
        $query = "UPDATE $ratingstable
                SET xar_rating = ?,
                    xar_numratings = ?
                WHERE xar_rid = ?";
        $bindvars = array($newrating, $newnumratings, $rid);
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

    } else {
        $result->close();

        // Get a new ratings ID
        $rid = $dbconn->GenId($ratingstable);
        // Create new rating
        $query = "INSERT INTO $ratingstable(xar_rid,
                                          xar_moduleid,
                                          xar_itemid,
                                          xar_itemtype,
                                          xar_rating,
                                          xar_numratings)
                VALUES (?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?)";
        $bindvars = array($rid, $modid, $objectid, $itemtype, $rating, 1);
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

        $newrating = $rating;
    }

    // Set note that user has rated this item if required
    if ($seclevel == 'high') {
        if (xarUserIsLoggedIn()) {
            xarModSetUserVar('ratings',$modname.':'.$itemtype.':'.$objectid,time());
        } else {
            // nope
        }
    } elseif ($seclevel == 'medium') {
        if (xarUserIsLoggedIn()) {
            xarModSetUserVar('ratings',$modname.':'.$itemtype.':'.$objectid,time());
        } else {
            xarSessionSetVar('ratings:'.$modname.':'.$itemtype.':'.$objectid,time());
        }
    }
    // CHECKME: find some cleaner way to update the page cache if necessary
    if (function_exists('xarOutputFlushCached') &&
        xarModGetVar('xarcachemanager','FlushOnNewRating')) {
        $modinfo = xarModGetInfo($modid);
        // this may not be agressive enough flushing for all sites
        // we could flush "$modinfo[name]-" to remove all output cache associated with a module
        xarOutputFlushCached("$modinfo[name]-user-display-");
    }
    return $newrating;
}
?>