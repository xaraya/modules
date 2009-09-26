<?php

/**
 * File: $Id$
 *
 * Modify or create a page - handler for DD entry-point
 *
 * @package Xaraya
 * @copyright (C) 2007 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.consil.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_new($args)
{
    xarVarFetch('itemtype', 'id', $itemtype, 0, XARVAR_NOT_REQUIRED);
    if (!empty($itemtype)) $args['ptid'] = $itemtype;

    return xarMod::guiFunc('xarpages', 'admin', 'modifypage', $args);
}

?>