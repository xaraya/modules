<?php

/**
 * utility function to pass item field definitions to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @returns array
 * @return array containing the item field definitions
 */
function xarpages_userapi_getitemfields($args)
{
    extract($args);

    $itemfields = array();

/*
    $itemfields['pid']  = array('name'  => 'pid',
                                'label' => xarML('Page ID'),
                                'type'  => 'itemid');
*/

    $itemfields['name'] = array('name'  => 'name',
                                'label' => xarML('Name'),
                                'type'  => 'textbox');

    $itemfields['desc'] = array('name'  => 'desc',
                                'label' => xarML('Description'),
                                'type'  => 'textarea_small');

    // TODO: add other static xarpages fields here if relevant

    // Note: DD fields (if any) will be added automatically by the DD Migrate Items function

    return $itemfields;
}

?>
