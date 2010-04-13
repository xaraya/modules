<?php

/*
 * Get the itemtype of the page type.
 */

function xarpages_userapi_gettypeitemtype($args)
{
    static $type_itemtype = NULL;

    if (isset($type_itemtype)) return $type_itemtype;

    // Get the itemtype of the page type.
    $pagetype = xarMod::apiFunc(
        'xarpages', 'user', 'get_type',
        array('name' => '@pagetype', 'dd_flag' => false, 'include_system' => true)
    );

    if (!empty($pagetype)) {
        $type_itemtype = $pagetype['id'];
    } else {
        // If it does not exist, then create it now.
        $type_itemtype = xarMod::apiFunc(
            'xarpages', 'admin', 'createtype',
            array('name' => '@pagetype', 'description' => 'System generated \'pagetype\' type','info' => array())
        );
    }
    return $type_itemtype;
}

?>