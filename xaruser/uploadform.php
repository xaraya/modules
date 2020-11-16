<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * Show the uploads form
 * @return array
 */
function uploads_user_uploadform()
{
    if (!xarSecurityCheck('AddUploads')) {
        return;
    }
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['file_maxsize'] = xarModVars::get('uploads', 'file.maxsize');

    return $data;
}
