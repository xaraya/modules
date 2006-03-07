<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * the main administration function
 */
function uploads_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditUploads')) return;
      xarResponseRedirect(xarModURL('uploads', 'admin', 'view'));
    // success
    return true;
}

?>