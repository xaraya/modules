<?php
/**
 * Eventhub Module
 *
 * @package modules
 * @subpackage eventhub module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * ItemCreate Hook Subject Observer
 *
**/
sys::import('xaraya.structures.hooks.observer');
class PubsubItemCreateObserver extends HookObserver implements ixarEventObserver
{
    public $module = 'pubsub';
    public function notify(ixarEventSubject $subject)
    {
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();
        extract($extrainfo);

    // This has to be an argument
    if (empty($objectid)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'object ID', 'createhook', 'pubsub');
        throw new Exception($msg);
    }
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'extrainfo', 'createhook', 'pubsub');
        throw new Exception($msg);
    }

        // validate parameters...
        // NOTE: this isn't strictly necessary, the hook subject will have already
        // taken care of validations and these values can be relied on to be pre-populated
        // however, just for completeness...        
        if (!isset($module) || !is_string($module) || !xarMod::isAvailable($module))
            $invalid['module'] = 1; 
        if (isset($itemtype) && !is_numeric($itemtype))
            $invalid['itemtype'] = 1;
        if (!isset($itemid) || !is_numeric($itemid))
            $invalid['itemid'] = 1;
        
        // NOTE: as of Jamaica 2.2.0 it's ok to throw exceptions in hooks, the subject handles them
        if (!empty($invalid)) {
            $args = array(join(',',$invalid), 'eventhub', 'hooks', 'ItemCreate');
            $msg = 'Invalid #(1) for #(2) module #(2) #(3) observer notify method';
            throw new BadParameterException($args, $msg);
        }

        // Do something


        // @checkme: the api func returns an array of info, only really need the id ?
        if (empty($itemid))
            // @todo: exception here?
            return $extrainfo;

        // the subject expects an array of extrainfo
        // return the merged array of extrainfo and the created itemid
        return $extrainfo += array('itemid' => $itemid);
        
        

    // Get arguments from argument array
    extract($args);

    // This has to be an argument
    if (empty($objectid)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'object ID', 'createhook', 'pubsub');
        throw new Exception($msg);
    }
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'extrainfo', 'createhook', 'pubsub');
        throw new Exception($msg);
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
        $modname = $extrainfo['module'];
    } else {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (!$modid) return $extrainfo; // throw back

    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if ($createwithstatus = xarModVars::get('pubsub',"$modname.$itemtype.createwithstatus") ) {
        if ($createwithstatus == 1) {
            if (isset($extrainfo['status']) & $extrainfo['status'] < 2 ) {
                return $extrainfo;
            }
        }
    }

    $templateid = xarModVars::get('pubsub',"$modname.$itemtype.create");
    if (!isset($templateid)) {
        $templateid = xarModVars::get('pubsub',"$modname.create");
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
            $extra = xarModGetIDFromName($extrainfo['current_module']);
        }
        if(isset($extrainfo['current_itemtype']) && is_numeric($extrainfo['current_itemtype'])) {
            $extra .= '-' . $extrainfo['current_itemtype'];
        }
        if(isset($extrainfo['current_itemid']) && is_numeric($extrainfo['current_itemid'])) {
            $extra .= '-' . $extrainfo['current_itemid'];
        }
    }
/* */

    // process the event (i.e. create a job for each subscription)
    if (!xarMod::apiFunc('pubsub','admin','processevent',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'cid' => $cid,
                             'extra' => $extra,
                             'objectid' => $objectid,
                             'templateid' => $templateid))) {
        // oops - but life goes on in hook functions :)
        return $extrainfo;
    }

    return $extrainfo;

 // END createhook
    }
}
?>