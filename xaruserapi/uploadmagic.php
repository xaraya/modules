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

function uploads_userapi_uploadmagic($args)
{
    $fileUpload = xarModAPIFunc('uploads', 'user', 'upload', $args);

    if (is_array($fileUpload)) {
        return '#file:' . $fileUpload['ulid'] . '#';
    } else {
        return $fileUpload;
    }
}
