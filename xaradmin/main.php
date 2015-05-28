<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * the main administration function
 * @return array
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminPubSub')) return;
//        xarController::redirect(xarModURL('pubsub', 'admin', 'viewall'));
    // Return the template variables defined in this function
    return array();
}

?>
