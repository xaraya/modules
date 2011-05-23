<?php

/**
 * File: $Id$
 *
 * Admin overview of all page types.
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_viewtypes($args)
{
    if (!xarSecurityCheck('EditXarpages')) {return;}

    extract($args);

    $types = xarMod::apiFunc(
        'xarpages', 'user', 'get_types',
        array('key' => 'index', 'dd_flag' => false)
    );

    if (empty($types)) $types = array();

    return array('types' => $types);
}

?>