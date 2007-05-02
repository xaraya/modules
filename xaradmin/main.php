<?php
/**
 * File: $Id$
 * 
 * Ping initialization functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage ping
 * @author John Cox
 */
/**
 * Main admin gui function, entry point
 *
 * @return bool
 */
function ping_admin_main()
{
// Security Check
    if(!xarSecurityCheck('Adminping')) return;
    // Return the output
    return array();
}
?>