<?php

/**
 * display an item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param $args an array of arguments (if called by other modules)
 * @param $args['objectid'] a generic object id (if called by other modules)
 * @param $args['exid'] the item id used for this example module
 */
function helpdesk_admin_display($args)
{
    if (!xarVarFetch('itemid',   'id', $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'id', $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('objectid', 'id', $objectid,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'user', 'display', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    $data['itemid'] = $itemid;
    $data['itemtype'] = $itemtype;

    if (!xarSecurityCheck('readhelpdesk',1,'Item',$itemid)) return;

    // get user settings for 'bold'
    $data['is_bold'] = xarModGetUserVar('helpdesk', 'bold');

    xarVarSetCached('Blocks.helpdesk', 'itemid', $itemid);
    $data['menu'] = xarModFunc('helpdesk','admin','menu');

    $item = array();
    $item['module'] = 'helpdesk';
    $item['returnurl'] = xarModURL('helpdesk', 'user', 'display',
                                   array('itemid' => $itemid));
    $data['hooks'] = xarModCallHooks('item', 'display', $itemid, $item);

    // Return the template variables defined in this function
    return $data;
}
?>
