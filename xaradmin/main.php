<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Overview Menu
 */
function comments_admin_main()
{
    if (!xarSecurity::check('AdminComments')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarController::redirect(xarController::URL('comments', 'admin', 'view'));
    }
    return true;
}
