<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function comments_admin_importblacklist()
{
    if (!xarSecurityCheck('Comments-Admin')) return;
    if (!xarModAPIFunc('comments', 'admin', 'import_blacklist')) return;
    return array();
}
?>