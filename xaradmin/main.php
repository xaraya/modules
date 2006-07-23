<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * the main administration function
 * @return array
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminPubSub')) return;
        xarResponseRedirect(xarModURL('pubsub', 'admin', 'viewall'));
    // Return the template variables defined in this function
    return array();
}

?>
