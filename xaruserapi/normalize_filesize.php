<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
function uploads_userapi_normalize_filesize( $args )
{

    if (is_array($args)) {
        extract($args);
    } elseif (is_numeric($args)) {
        $fileSize = $args;
    } else {
        return array('long' => 0, 'short' => 0);
    }

    $size = $fileSize;

    $range = array('', 'KB', 'MB', 'GB', 'TB', 'PB');

    for ($i = 0; $size >= 1024 && $i < count($range); $i++) {
        $size /= 1024;
    }

    $short = round($size, 2).' '.$range[$i];

    return array('long' => number_format($fileSize), 'short' => $short);
}
?>
