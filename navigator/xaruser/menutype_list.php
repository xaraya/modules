<?php
/*
 * File: $Id: $
 *
 * @package Navigator
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author "Carl P. Corliss" <ccorliss@schwabfoundation.org>
*/

/**
 * Display's the List type menu.
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_user_menutype_list( $args )
{

    $data = xarModAPIFunc('navigator', 'user', 'process_menu_attributes', $args);

    if (isset($data['current_primary_id']) && $data['current_primary_id'] == 0) {
         return;
    }

    if (!isset($data) || empty($data)) {
        return;
    } else {
        extract($data);
        extract($args);
    }

    foreach ($tree as $key => $item) {
        unset($item['npid']);
        unset($item['ncid']);

        if (!isset($first)) {
            $first = TRUE;
            $list[1] = $item;
        } else {
            $list[] = $item;
        }
    }

    if (!isset($list) || empty($list)) {
        $list = array();
    }

    $data['matrix']   = $matrix;
    $data['primary']  = $primary;
    $data['tree']     = $list;
    return $data;
}

?>
