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
 * Import a file
 * @param string importFrom
 * @return mixed
 */
function uploads_user_upload()
{
    if (!xarSecurityCheck('AddUploads')) {
        return;
    }

    xarVarFetch('importFrom', 'str:1:', $importFrom, null, XARVAR_NOT_REQUIRED);

    $list = xarModAPIFunc(
        'uploads',
        'user',
        'process_files',
        array('importFrom' => $importFrom)
    );

    if (is_array($list) && count($list)) {
        return array('fileList' => $list);
    } else {
        xarController::redirect(xarModURL('uploads', 'user', 'uploadform'));
    }
}
