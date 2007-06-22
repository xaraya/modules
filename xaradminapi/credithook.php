<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * create linkage for an item - hook for ('item','create','API')
 * Needs $extrainfo['cids'] from arguments, or 'cids' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function recommend_adminapi_credithook($args)
{
    extract($args);
    
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // see if we have anything to do here (might be empty => return)
    if (!isset($extrainfo['itemid'])) {
        
    }
    
    $newuid = $extrainfo['itemid'];

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'admin', 'credithook', 'recommend');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'credithook', 'recommend');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    $recipient_email = xarUserGetVar('email', $newuid);
    
    $recommender = xarModAPIFunc('recommend', 'user', 'getcredit', array('recipient_email' => $recipient_email));
    
    if($recommender) {

        // Call create hooks for categories, hitcount etc.
        $args['userid'] = $recommender['sentby_uid'];
        
        // Specify the module, itemtype and itemid so that the right hooks are called
        $args['module'] = 'recommend';
        $args['itemtype'] = 0;
        $args['itemid'] = $recommender['recipientid'];
//       echo "args: <pre>";print_r($args);die("</pre>");
        xarModCallHooks('item', 'credit', $recommender['recipientid'], $args);
    }
    
    // Return the extra info
    return $extrainfo;
}

?>
