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
    extract($args);

    // Security check
    if (!xarSecurityCheck('EditXarpagesPage', 1)) {return false;}

    $types = xarModAPIFunc(
        'xarpages', 'user', 'get_types',
        array('key' => 'index', 'dd_flag' => false)
    );

    if (empty($types)) $types = array();

    return array('types' => $types);
}

?>