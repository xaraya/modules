<?php

/**
 * add new item
 */
function logconfig_admin_new()
{
    $data = xarModAPIFunc('logconfig','admin','menu');

    if (!xarVarFetch('itemtype','id',$itemtype)) return;
    if (!xarSecurityCheck('AdminLogConfig')) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $data['object'] =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'logconfig',
                                              'itemtype' => $itemtype));
     $data['itemtype'] = $itemtype;
     
    // Return the template variables defined in this function
    return $data;
}

?>