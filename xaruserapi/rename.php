<?php
/**
* Rename a file or folder
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Rename a file or folder
*
* If 'name' not given, show the GUI.  Otherwise, create it.
*
* @param  $args ['path'] path of the file relative to archive_dir
* @param  $args ['name'] name to change the file to
* @returns bool
* @return true on success, false on failure
* @raise BAD_PARAM, NO_PERMISSION
*/
function files_userapi_rename($args)
{
    // security check
    if (!xarSecurityCheck('DeleteFiles', 1)) return;

    extract($args);

    // validate inputs
    $invalid = array();
    if (!isset($name) || empty($name) || !is_string($name) || strstr($name, '/')) {
        $invalid[] = 'name';
    }
    if (!isset($path) || empty($path) || !is_string($path) || $path == '/') {
        $invalid[] = 'path';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'rename', 'files');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get some paths
    $archive_dir = xarModGetVar('files', 'archive_dir');
    $realpath = realpath("$archive_dir/$path");
    $oldpath = $realpath;
    $basepath = dirname($realpath);
    $newpath = "$basepath/$name";

    // perform renaming function
    if (!rename($oldpath, $newpath)) {
        return;
    }

    // success
    return true;
}

?>