<?php
/**
* Create a file or folder
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
* Create a file or folder
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param   string $args['path'] Path of folder to create in, with relative path
* @param   string $args['name'] Name of new item
* @param   string $args['itemtype'] 'folder' is default. can also be 'file'.
* @return  boolean
* @returns true if successful
* @throws  BAD_PARAM, NO_PERMISSION
*/
function files_userapi_create($args)
{
    // security check
    if (!xarSecurityCheck('AddFiles', 1)) return;

    extract($args);

    // validate inputs
    $invalid = array();
    if (!isset($name) || empty($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($path) || empty($path) || !is_string($path)) {
        $invalid[] = 'path';
    }
    if (isset($itemtype) && ($itemtype != 'folder' && $itemtype != 'file')) {
        $invalid[] = 'itemtype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'create', 'files');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // set defaults
    if (empty($itemtype)) $itemtype = 'folder';

    // get some paths
    $archive_dir = xarModGetVar('files', 'archive_dir');
    $basepath = realpath("$archive_dir/$path");

    // create any new folders if specified
    $parts = explode('/', $name);
    $lastpart = array_pop($parts);
    $newpath = "$basepath/";
    foreach ($parts as $part) {
        $newpath .= "$part/";
        if (!is_dir($newpath) && !mkdir($newpath, 0755)) {
            return;
        }
    }

    // create last item in path (file or folder)
    switch($itemtype) {
        case 'file':
            if (!touch("$newpath/$lastpart")) {
                return;
            }
            break;
        case 'folder': default:
            $newpath .= "$lastpart/";
            if (!is_dir($newpath) && !mkdir($newpath, 0755)) {
                return;
            }
            break;
        }

    // success
    return true;
}

?>
