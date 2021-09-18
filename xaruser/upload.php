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
    if (!xarSecurity::check('AddUploads')) {
        return;
    }

    xarVar::fetch('importFrom', 'str:1:', $importFrom, null, xarVar::NOT_REQUIRED);

    $list = xarMod::apiFunc(
        'uploads',
        'user',
        'process_files',
        ['importFrom' => $importFrom]
    );

    if (is_array($list) && count($list)) {
        return ['fileList' => $list];
    } else {
        xarController::redirect(xarController::URL('uploads', 'user', 'uploadform'));
    }
}
