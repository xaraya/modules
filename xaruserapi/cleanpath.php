<?php
/**
* Clean up and validate a path
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
* Clean up and validate a path
*
* Checks that the path exists on the file system and gets rid of
* things like double slashes.
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public / private / protected
* @param   string $args['path'] Relative path to be cleaned
* @param   string $args['mode'] 'read' or 'write' - read is default
* @param   string $args['type'] optional type of item. 'file' or 'folder'
* @return  string
* @returns cleaned up path
* @throws  BAD_PARAM
*/
function files_userapi_cleanpath($args)
{
    extract($args);

    // set defaults
    if (empty($mode)) $mode = 'read';
    if (empty($type)) $type = '';

    // If path not given, it's no use continuing.  Go to root dir.
    if (empty($path)) {
        return '';
    }

    // translate elements back to usable characters
    $path = rawurldecode($path);

    // clean up slashes
    $path = preg_replace("/\/+/", '/', $path); // remove double slashes
    if (strlen($path) > 1) {
        $path = preg_replace("/\/\$/", '', $path); // remove trailing slash
    }

    // get archive directory
    $archive_dir = xarModGetVar('files', 'archive_dir');

    // don't allow anything above archive_dir
    if (strlen(realpath("$archive_dir/$path")) < strlen(realpath($archive_dir))) {
        $msg = xarML('Path #(1) is outside of archive directory.', $path);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // validate read/write status
    $error = false;
    switch ($mode) {
        case 'write':
            if (!is_writable("$archive_dir/$path")) {
                $error = true;
                $msg = xarML('Path #(1) is not writable by the web server', $path);
            }
            break;
        case 'read': default:
            if (!is_readable("$archive_dir/$path")) {
                $error = true;
                $msg = xarML('Path #(1) is not readable by the web server', $path);
            }
            break;
    }
    if ($error) {
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // validate type
    $error = false;
    if (!empty($type)) {
        $error = false;
        switch ($type) {
            case 'file':
                if (!is_file("$archive_dir/$path")) {
                    $error = true;
                    $msg = xarML('Path #(1) is not a regular file', $path);
                }
                break;
            case 'link':
                if (!is_link("$archive_dir/$path")) {
                    $error = true;
                    $msg = xarML('Path #(1) is not a link', $path);
                }
                break;
            case 'folder': default:
                if (!is_dir("$archive_dir/$path")) {
                    $error = true;
                    $msg = xarML('Path #(1) is not a folder', $path);
                }
                break;
        }
        if ($error) {
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    }

    // success
    return $path;
}

?>
