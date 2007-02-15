<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * Show the uploads form
 * @return array
 */
function uploads_user_uploadform()
{
    if (!xarSecurityCheck('AddUploads')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['file_maxsize'] = xarModGetVar('uploads','file.maxsize');

    return $data;
}
?>