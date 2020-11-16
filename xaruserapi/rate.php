<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * rate an item
 *
 * @param $args['modname'] module name of the item to rate
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemid'] ID of the item to rate
 * @param $args['rating'] actual rating
 * @return int the new rating for this item
 */
function ratings_userapi_rate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($itemid)) ||
        (!isset($rating) || !is_numeric($rating) || $rating < 0 || $rating > 100)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            xarML('value'),
            'user',
            'rate',
            'ratings'
        );
        throw new Exception($msg);
    }
    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            xarML('module id'),
            'user',
            'rate',
            'ratings'
        );
        throw new Exception($msg);
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    // Security Check
    if (!xarSecurityCheck('CommentRatings', 1, 'Item', "$modname:$itemtype:$itemid")) {
        return;
    }


    // Database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $ratingstable = $xartable['ratings'];

    // Multipe rate check
    if (!empty($itemtype)) {
        $seclevel = xarModVars::get('ratings', "seclevel.$modname.$itemtype");
        if (!isset($seclevel)) {
            $seclevel = xarModVars::get('ratings', 'seclevel.'.$modname);
        }
    } else {
        $seclevel = xarModVars::get('ratings', 'seclevel.'.$modname);
    }
    if (!isset($seclevel)) {
        $seclevel = xarModVars::get('ratings', 'seclevel');
    }
    if ($seclevel == 'high') {
        if (xarUserIsLoggedIn()) {
            $rated = xarModUserVars::get('ratings', $modname.':'.$itemtype.':'.$itemid);
            if (!empty($rated) && $rated > 1) {
                return;
            }
        } else {
            return;
        }
    } elseif ($seclevel == 'medium') {
        // Check to see if user has already voted
        if (xarUserIsLoggedIn()) {
            $rated = xarModUserVars::get('ratings', $modname.':'.$itemtype.':'.$itemid);
            if (!empty($rated) && $rated > time() - 24*60*60) {
                return;
            }
        } else {
            $rated = xarSession::getVar('ratings:'.$modname.':'.$itemtype.':'.$itemid);
            if (!empty($rated) && $rated > time() - 24*60*60) {
                return;
            }
        }
    } // No check for low

    // Get current information on rating
    $query = "SELECT id,
                   rating,
                   numratings
            FROM $ratingstable
            WHERE module_id = ?
              AND itemid = ?
              AND itemtype = ?";
    $bindvars = array($modid, $itemid, $itemtype);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }

    if (!$result->EOF) {
        // Update current rating
        list($id, $currating, $numratings) = $result->fields;
        $result->close();

        // Calculate new rating
        $newnumratings = $numratings + 1;
        $newrating = (($currating*$numratings) + $rating)/$newnumratings;

        // Insert new rating
        $query = "UPDATE $ratingstable
                SET rating = ?,
                    numratings = ?
                WHERE id = ?";
        $bindvars = array($newrating, $newnumratings, $id);
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }
    } else {
        $result->close();

        // Get a new ratings ID
        $id = $dbconn->GenId($ratingstable);
        // Create new rating
        $query = "INSERT INTO $ratingstable(id,
                                          module_id,
                                          itemid,
                                          itemtype,
                                          rating,
                                          numratings)
                VALUES (?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?)";
        $bindvars = array($id, $modid, $itemid, $itemtype, $rating, 1);
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }

        $newrating = $rating;
    }

    // Set note that user has rated this item if required
    if ($seclevel == 'high') {
        if (xarUserIsLoggedIn()) {
            xarModUserVars::set('ratings', $modname.':'.$itemtype.':'.$itemid, time());
        } else {
            // nope
        }
    } elseif ($seclevel == 'medium') {
        if (xarUserIsLoggedIn()) {
            xarModUserVars::set('ratings', $modname.':'.$itemtype.':'.$itemid, time());
        } else {
            xarSession::setVar('ratings:'.$modname.':'.$itemtype.':'.$itemid, time());
        }
    }
    // CHECKME: find some cleaner way to update the page cache if necessary
    if (function_exists('xarOutputFlushCached') &&
        xarModVars::get('xarcachemanager', 'FlushOnNewRating')) {
        $modinfo = xarMod::getInfo($modid);
        // this may not be agressive enough flushing for all sites
        // we could flush "$modinfo[name]-" to remove all output cache associated with a module
        xarOutputFlushCached("$modinfo[name]-user-display-");
    }
    return $newrating;
}
