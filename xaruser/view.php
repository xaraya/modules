<?php
/**
 * Standard Xaraya function in this case redirects to main user function
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
*/
/**
 * Redirect to the main function with same args
 */
function xarbb_user_view($args)
{
    // No security check or redirect needed - just call up the main function.
    return xarModFunc('xarbb', 'user', 'main', $args);
}

?>