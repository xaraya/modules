<?php

/**
 * add new item
 */
function logconfig_admin_newloggers()
{
    $data = xarModAPIFunc('logconfig','admin','menu');

    if (!xarSecurityCheck('AdminLogConfig')) return;

    //This is used in admin/view
    $itemsnum = xarModGetVar('logconfig','itemstypenumber');

    $data['objects'] = array();
    for ($itemtype = 1; $itemtype <= $itemsnum; $itemtype++)
    {
        $object =& xarModAPIFunc('dynamicdata','user','getobjectlist',
                                         array('module' => 'logconfig',
                                                  'itemtype' => $itemtype));
         $data['objects'][$itemtype] = array ('type' => $object->properties['loggerType']->default,
                                                                       'description' => $object->properties['description']->default);
    }

    // Return the template variables defined in this function
    return $data;
}

?>
