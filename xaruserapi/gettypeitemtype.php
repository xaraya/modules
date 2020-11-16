<?php

/*
 * Get the itemtype of the page type.
 */

function xarpages_userapi_gettypeitemtype($args)
{
    static $type_itemtype = null;

    if (isset($type_itemtype)) {
        return $type_itemtype;
    }

    // Get the itemtype of the page type.
    $pagetype = xarMod::apiFunc(
        'xarpages',
        'user',
        'gettype',
        array('name' => '@pagetype', 'dd_flag' => false, 'include_system' => true)
    );

    if (!empty($pagetype)) {
        $type_itemtype = $pagetype['ptid'];
    } else {
        // If it does not exist, then create it now.
        $type_itemtype = xarMod::apiFunc(
            'xarpages',
            'admin',
            'createtype',
            array('name' => '@pagetype', 'desc' => 'System generated \'pagetype\' type')
        );
    }

    return $type_itemtype;
}
