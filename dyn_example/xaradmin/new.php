<?php

/**
 * add new item
 */
function dyn_example_admin_new()
{
    $data = xarModAPIFunc('dyn_example','admin','menu');

    if (!xarSecurityCheck('AddDynExample')) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $data['object'] =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'dyn_example'));

    $item = array();
    $item['module'] = 'dyn_example';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
