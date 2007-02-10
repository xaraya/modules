<?php

/**
 * File: $Id$
 *
 * Create a new page.
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_newpage($args)
{
    return xarModFunc('xarpages', 'admin', 'modifypage', $args);
}

?>