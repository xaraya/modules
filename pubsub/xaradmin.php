<?php
/**
 * File: $Id$
 *
 * Pubsub admin main
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * the main administration function
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminPubSub')) return;

    // Return the template variables defined in this function
    return array();
}

?>
