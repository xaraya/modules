<?php
/**
* Delete a file or folder
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
* Delete a file or folder
*
* Remove a file or recursively remove a folder and its contents
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param   string $args['path'] Path of item to delete, with relative path
* @return  boolean
* @returns true if successful
* @throws  BAD_PARAM, NO_PERMISSION
*/
function files_userapi_delete($args)
{
    if (!xarSecurityCheck('DeleteFiles', 1)) return;

    extract($args);

    // clean up the path and prepare to validate it
    $path = xarModAPIFunc('files', 'user', 'cleanpath', array('path' => $path));
    if (empty($path) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get some paths
    $archive_dir = xarModGetVar('files', 'archive_dir');
    $realpath = realpath("$archive_dir/$path");

    // use utility function to recursively delete
    if (!files__rm($realpath)) {
        return;
    }

    // success
    return true;
}


/**
* Delete files and directories
*
* Recursive deletion similar to *nix rm() command
*
* @author bishop <http://us2.php.net/manual/en/function.unlink.php>
* @access  private
* @param $fileglob mixed If string, must be a file name (foo.txt), glob pattern (*.txt), or directory name.
*                        If array, must be an array of file names, glob patterns, or directories.
* @return  boolean
* @returns true if successful
* @throws  BAD_PARAM
*/
function files__rm($fileglob)
{
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
            return unlink($fileglob);
        } else if (is_dir($fileglob)) {
            $ok = files__rm("$fileglob/*");
            if (! $ok) {
                return false;
            }
            return rmdir($fileglob);
        } else {
            $matching = glob($fileglob);
            if ($matching === false) {
                trigger_error(sprintf('No files match supplied glob %s', $fileglob),
                    E_USER_WARNING);
                return false;
            }
            $rcs = array_map('files__rm', $matching);
            if (in_array(false, $rcs)) {
                return false;
            }
        }
    } else if (is_array($fileglob)) {
        $rcs = array_map('files__rm', $fileglob);
        if (in_array(false, $rcs)) {
            return false;
        }
    } else {
        trigger_error('Param #1 must be filename or glob pattern, or array of filenames '
            . 'or glob patterns', E_USER_ERROR);
        return false;
    }

    return true;
}

?>
