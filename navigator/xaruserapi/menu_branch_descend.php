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

function navigator_userapi_menu_branch_descend( $args )
{
    extract($args);

    if (!isset($tree)) {
        return '';
    } else {
        return xarTplModule('navigator', 'user', 'menutype_branch', array('tree' => $tree));
    }
}

?>