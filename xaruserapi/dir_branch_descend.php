<?php
/*
 * File: $Id: $
 *
 * @package Uploads
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage uploads module
 * @author "Carl P. Corliss" <ccorliss@schwabfoundation.org>
*/

/**
 * Used to recursively (inside a template) display a nested array directories
 *
 * @author Carl P. Corliss
 * @copyright 2004 (c) The Charles and Helen Schwab Foundation
 */

function uploads_userapi_dir_branch_descend( $args )
{
    extract($args);

    static $level = 0;
    static $firstTime = TRUE;

    if (!isset($tree) || !is_array($tree)) {
        return '';
    } else {

        $data['tree'] = $tree;

        $output = xarTplModule('uploads', 'user', 'directory_branch', $data);

        return $output;
    }
}

?>
