<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with smilies Menu information
 */
function smilies_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditSmilies')) return;

        xarResponseRedirect(xarModURL('smilies', 'admin', 'view'));

    // success
    return true;
}
?>