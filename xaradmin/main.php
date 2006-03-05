<?php
/**
 * Chat Module - Port of PJIRC for Xaraya
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Chat Module
 * @link http://xaraya.com/index.php/release/158.html
 * @author John Cox
 */
/**
 * Add a standard screen upon entry to the module.
 *
 * @return output with censor Menu information
 */
function chat_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminChat')) return;
    // we only really need to show the default view (overview in this case)
    return array();
}


?>