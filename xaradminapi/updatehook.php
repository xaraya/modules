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
 * handle a pubsub 'update' event
 * update event for an item - hook for ('item','update','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array $extrainfo, like any hook function should :)
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatehook($args)
{
    // Get arguments from argument array
    extract($args);
    // This has to be an argument
    if (empty($objectid)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'object ID', 'updatehook', 'pubsub');
        throw new Exception($msg);
    }
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'extrainfo', 'updatehook', 'pubsub');
        throw new Exception($msg);
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
        $modname = $extrainfo['module'];
    } else {
        $modname = xarMod::getName();
    }
    $modid = xarMod::getRegId($modname);
    if (!$modid) return $extrainfo; // throw back

    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    $typeoftemplate = 'update';
    if ($createwithstatus = xarModVars::get('pubsub',"$modname.$itemtype.createwithstatus") ) {
        if ($createwithstatus == 1 & $extrainfo['status'] >= 2 & $extrainfo['oldstatus']< 2) {
            $typeoftemplate = 'create';
        }
    }

    $id = xarModVars::get('pubsub',"$modname.$itemtype.$typeoftemplate");
    if (!isset($id)) {
        $id = xarModVars::get('pubsub',"$modname.$typeoftemplate");
    }
    // if there's no 'update' template defined for this module(+itemtype), we're done here
    if (empty($id)) {
        return $extrainfo;
    }

// FIXME: get categories for updated item
    $cid = '';
    if (isset($extrainfo['cid']) && is_numeric($extrainfo['cid'])) {
        $cid = $extrainfo['cid'];
    } elseif (isset($extrainfo['cids'][0]) && is_numeric($extrainfo['cids'][0])) {
    // TODO: loop over all categories
        $cid = $extrainfo['cids'][0];
    } else {
        // Do nothing if we do not get a cid.
        return $extrainfo;
    }

    $extra = null;
// FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    if ($modname == 'comments') {
        $extra = '';
        if (isset($extrainfo['current_module']) && is_string($extrainfo['current_module'])) {
            $extra = xarMod::getRegId($extrainfo['current_module']);
        }
        if(isset($extrainfo['current_itemtype']) && is_numeric($extrainfo['current_itemtype'])) {
            $extra .= '-' . $extrainfo['current_itemtype'];
        }
        if(isset($extrainfo['current_itemid']) && is_numeric($extrainfo['current_itemid'])) {
            $extra .= '-' . $extrainfo['current_itemid'];
        }
    }

    // process the event (i.e. create a job for each subscription)
    if (!xarMod::apiFunc('pubsub','admin','processevent',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'cid' => $cid,
                             'extra' => $extra,
                             'objectid' => $objectid,
                             'template_id' => $templateid))) {
        // oops - but life goes on in hook functions :)
        return $extrainfo;
    }

    return $extrainfo;

} // END updatehook

?>
