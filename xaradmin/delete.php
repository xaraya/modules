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

function xarpages_admin_modify($args)
{
    return xarModfunc('xarpages', 'admin', 'deletepage', $args);
}

?>