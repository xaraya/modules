<?php

/**
 * modify an item
 */
function logconfig_admin_modify($args)
{
    list($itemid,
         $objectid,
         $itemtype)= xarVarCleanFromInput('itemid',
                                                                      'objectid',
                                                                      'itemtype');

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'modify', 'logconfig');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $object =& xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'logconfig',
                                   'itemid' => $itemid,
                                   'itemtype' => $itemtype));
    if (!isset($object)) return;

    // get the values for this item
    $newid = $object->getItem();
    if (!isset($newid) || $newid != $itemid) return;

    if (!xarSecurityCheck('AdminLogConfig')) return;

    $data = xarModAPIFunc('logconfig','admin','menu');
    $data['itemid'] = $itemid;
    $data['itemtype'] = $itemtype;
    $data['object'] =& $object;

    // Return the template variables defined in this function
    return $data;
}

?>