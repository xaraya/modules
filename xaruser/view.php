<?php
/**
 * Standard Xaraya function in this case redirects to main user function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_view()
{
    // Security Check
    if(!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;
    
    xarResponseRedirect(xarModURL('xarbb', 'user', 'main'));

    return true;
}

?>