<?php
/**
 * xarBB - Jump to another forum
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage  xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 */
/**
 * Jump to a forum
 *
 * @param int f ForumID
 * @return bool true if correctly jumped to forum
 */
function xarbb_user_jump()
{
    // Security Check
    if (!xarSecurityCheck('ViewxarBB', 1, 'Forum')) return;
    if (!xarVarFetch('f', 'isset', $f, NULL, XARVAR_DONT_SET)) return;
    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $f)));
    return true;
}
?>