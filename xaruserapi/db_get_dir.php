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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 *  Retrieve a directory path
 *
 * @param  string directory designation
 * @return string relative path of the directory
 */

function uploads_userapi_db_get_dir($args)
{
    extract($args);

    if (!isset($directory)) {
        $msg = xarML('Missing [#(1)] parameter for function [#(2)] in module [#(3)]', 'directory', 'db_get_dir', 'uploads');
        throw new Exception($msg);
    }

    $root = sys::root();
    if (empty($root)) {
        $directory = xarModVars::get('uploads', $directory);
    } else {
        $directory = sys::root() . "/" . xarModVars::get('uploads', $directory);
    }
    return $directory;
}
