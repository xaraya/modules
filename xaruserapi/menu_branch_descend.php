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
 * Used to recursively (inside a template) display a nested array of categories
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function navigator_userapi_menu_branch_descend( $args )
{
    extract($args);
    static $level = 0;
    static $firstTime = TRUE;


    $level++;

    if (!isset($tree)) {
        return '';
    } else {
        $data['tree'] = $tree;
        $data['level'] = $level;

        if ($firstTime) {
            $data['firstTime'] = TRUE;
        } else {
            $firstTime = FALSE;
            $data['firstTime'] = FALSE;
        }

        if (isset($showStartPoint)) {
            $data['showStartPoint'] = $showStartPoint;

        }

        $output = xarTplModule('navigator', 'user', 'menutype_branch', $data);
        $level--;

        return $output;
    }
}

?>