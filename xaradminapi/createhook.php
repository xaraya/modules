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
 * handle a pubsub 'create' event
 * create event for an item - hook for ('item','create','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array $extrainfo, like any hook function should :)
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_createhook($args)
{
    // Get arguments from argument array
    extract($args);

    // This has to be an argument
    if (empty($objectid)) {
        $msg = xarML(
            'Invalid #(1) in function #(2)() in module #(3)',
            'object ID',
            'createhook',
            'pubsub'
        );
        throw new Exception($msg);
    }
    if (!isset($extrainfo)) {
        $msg = xarML(
            'Invalid #(1) in function #(2)() in module #(3)',
            'extrainfo',
            'createhook',
            'pubsub'
        );
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
    if (!$modid) {
        return $extrainfo;
    } // throw back

    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if ($createwithstatus = xarModVars::get('pubsub', "$modname.$itemtype.createwithstatus")) {
        if ($createwithstatus == 1) {
            if (isset($extrainfo['status']) & $extrainfo['status'] < 2) {
                return $extrainfo;
            }
        }
    }

    $templateid = xarModVars::get('pubsub', "$modname.$itemtype.create");
    if (!isset($templateid)) {
        $templateid = xarModVars::get('pubsub', "$modname.create");
    }
    // if there's no 'create' template defined for this module(+itemtype), we're done here
    if (empty($templateid)) {
        return $extrainfo;
    }

    //FIXME: <garrett> During an article->create $extrainfo['cid'] does not exist. Instead
    // the array $extrainfo['cids'] exists. Is this because an article can have
    // multiple categories? - yes
    // Q: What is hcid? it's in the extrainfo... - hitcount id
    // Q: If cid is an array, why are we returning a singleton? I think we should be
    // subscribing the user to all cats assoc'd with the article, thus creating
    // multiple events - not create events, but process them instead :-)
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
    /* */
    // FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    if ($modname == 'comments') {
        $extra = '';
        if (isset($extrainfo['current_module']) && is_string($extrainfo['current_module'])) {
            $extra = xarMod::getRegId($extrainfo['current_module']);
        }
        if (isset($extrainfo['current_itemtype']) && is_numeric($extrainfo['current_itemtype'])) {
            $extra .= '-' . $extrainfo['current_itemtype'];
        }
        if (isset($extrainfo['current_itemid']) && is_numeric($extrainfo['current_itemid'])) {
            $extra .= '-' . $extrainfo['current_itemid'];
        }
    }
    /* */

    // process the event (i.e. create a job for each subscription)
    if (!xarMod::apiFunc(
        'pubsub',
        'admin',
        'processevent',
        array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'cid' => $cid,
                             'extra' => $extra,
                             'objectid' => $objectid,
                             'templateid' => $templateid)
    )) {
        // oops - but life goes on in hook functions :)
        return $extrainfo;
    }

    return $extrainfo;
} // END createhook
