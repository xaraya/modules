<?php

/*
 * Get the itemtype of the page type.
 */

function xarpages_userapi_gettypeitemtype($args)
{
    static $type_itemtype = NULL;

    if (isset($type_itemtype)) {
        return $type_itemtype;
    }

    // Get the itemtype of the page type.
    $pagetype = xarModAPIfunc(
        'xarpages', 'user', 'gettype',
        array('name' => '@pagetype', 'dd_flag' => false, 'include_system' => true)
    );

    if (!empty($pagetype)) {
        $type_itemtype = $pagetype['ptid'];
    }

    return $type_itemtype;
}

?>