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
function xarbb_user_view($args)
{
    // No security check or redirect needed - just call up the main function.
    return xarModFunc('xarbb', 'user', 'main', $args);
}

?>