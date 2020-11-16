<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * display pubsub element next to a registered event
 *  - A subscribe icon is if the user is registered
 *  - Nothing is displayed for an unregisted user
 *  - An unsubscribe option is displayed to users currently subscribed
 *
 * @param $args['extrainfo'] category, module, itemtype and URL to return
 * @param $args['layout'] layout to use (icon or text) - not when using hooks
 * @return array output with pubsub information
 */
function pubsub_user_displayicon($args)
{
    extract($args);

    // do nothing if user not logged in otherwise subscribe
    // the currently logged in user
    if (!xarUser::isLoggedIn()) {
        return;
    }

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    /**
     * Validate parameters
     */
    $invalid = array();
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $invalid[] = 'extrainfo';
    } elseif (isset($extrainfo['cid'])) {
        $cid = $extrainfo['cid'];

        if (isset($extrainfo['cid']) && is_numeric($extrainfo['cid'])) {
            $cid = $extrainfo['cid'];
        }
        // FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
        if (isset($extrainfo['current_module']) && is_string($extrainfo['current_module'])) {
            $modname = $extrainfo['current_module'];
        } elseif (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        }
        if (isset($extrainfo['current_itemtype']) && is_numeric($extrainfo['current_itemtype'])) {
            $itemtype = $extrainfo['current_itemtype'];
        } elseif (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
        if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
            $returnurl = $extrainfo['returnurl'];
        }
        // this contains extra information, e.g. moduleid-itemtype-itemid of the original item for comments
        if (isset($extrainfo['extra']) && is_string($extrainfo['extra'])) {
            $extra = $extrainfo['extra'];
        }
    } else {
        // May only subscribe to categories, no category, pubsub does nothing.
        return array('donotdisplay' => true);
    }

    if (count($invalid) > 0) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid),
            'user',
            'displayicon',
            'pubsub'
        );
        throw new Exception($msg);
    } else {
    }


    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($modname)) {
        $modname = xarMod::getName();
    }

    $modid = xarMod::getRegId($modname);

    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($returnurl)) {
        $returnurl = rawurlencode(xarServer::getCurrentURL());
    }

    // if pubsub isn't hooked to this module & itemtype, don't show subscription either
    if (!xarModHooks::isHooked('pubsub', $modname, $itemtype)) {
        return array('donotdisplay'=>true);
    }

    /// check for unsubscribe
    /**
     * Fetch the eventid to check
     */
    // Database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $pubsubeventstable = $xartable['pubsub_events'];
    $pubsubsubscriptionstable = $xartable['pubsub_subscriptions'];

    $query = "SELECT pubsubid
                FROM $pubsubeventstable, $pubsubsubscriptionstable
               WHERE $pubsubeventstable.modid = ?
                 AND $pubsubeventstable.itemtype = ?
                 AND $pubsubeventstable.cid = ?
                 AND $pubsubeventstable.eventid = $pubsubsubscriptionstable.eventid
                 AND $pubsubsubscriptionstable.userid = ?";

    $bindvars = array((int)$modid, (int)$itemtype, (int)$cid, (int)$userid);
    if (isset($extra)) {
        $query .= " AND $pubsubeventstable.extra = ?";
        array_push($bindvars, $extra);
    }
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }
    if ($result->EOF) {
        /**
         * If we get a hit on pubsub_reg, that mean we are already subscribed
         */
        $data['subscribe'] = 1;
    } else {
        $data['subscribe'] = 0;
    }

    $data['subdata'] = array('modname' => $modname
                             ,'modid'   => $modid
                             ,'itemtype' => $itemtype
                             ,'cid'     => $cid
                             ,'extra'   => isset($extra) ? $extra : null
                             ,'returnurl' => $returnurl
                             ,'subaction' => $data['subscribe']
                             );

    $data['subURL'] = xarController::URL('pubsub', 'user', 'modifysubscription', $data['subdata']);
    $data['subTEXT'] = xarML('Subscribe');

    $data['unsubURL'] = xarController::URL('pubsub', 'user', 'modifysubscription', $data['subdata']);
    $data['unsubTEXT'] = xarML('Unsubscribe');

    if (!empty($layout)) {
        $data['layout'] = $layout;
    } else {
        $data['layout'] = 'icon';
    }
    return $data;
} // END displayicon
