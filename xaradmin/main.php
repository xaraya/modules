<?php
/**
 * Main administration function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Xaraya Team
 */
/**
 * the main administration function
 * @return bool true on success of redirect
 */
function encyclopedia_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditEncyclopedia')) return;
   // xarResponseRedirect(xarModURL('encyclopedia', 'admin', 'getrecent'));
    // success
   // return true;
   return array ();
}
?>