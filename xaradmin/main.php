<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Overview Menu
 */
function comments_admin_main()
{
    if(!xarSecurityCheck('AdminComments')) {
        return;
    }
    // we only really need to show the default view (stats in this case)
    xarResponse::redirect(xarModURL('comments', 'admin', 'stats'));
    // success
    return true;
}
?>
