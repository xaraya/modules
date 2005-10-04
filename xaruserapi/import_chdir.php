<?php

/**
 *  Change to the specified directory within the local imports sandbox directory
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string     dirName  The name of the directory (within the import sandbox) to change to
 *  @returns string           The complete path to the new Current Working Directory within the sandbox
 */

function filemanager_userapi_import_chdir( $args )
{
    extract ( $args );

    if (!isset($dirName) || empty($dirName)) {
        $dirName = NULL;
    }

    $cwd = xarModGetUserVar('filemanager', 'path.cwd');
    $importDir = xarModGetVar('filemanager', 'path.imports-directory');

    if (!empty($dirName)) {
        if ($dirName == '...') {
            if (stristr($cwd, $importDir) && strlen($cwd) > strlen($importDir)) {
                $cwd = dirname($cwd);
                xarModSetUserVar('filemanager', 'path.cwd', $cwd);
            }
        } else {
            if (file_exists("$cwd/$dirName") && is_dir("$cwd/$dirName")) {
                $cwd = "$cwd/$dirName";
                xarModSetUserVar('filemanager', 'path.cwd', $cwd);
            }
        }
    } else {
        // if dirName is empty, then reset the cwd to the top level directory
        $cwd = xarModGetVar('filemanager', 'path.imports-directory');
        xarModSetUserVar('filemanager', 'path.cwd', $cwd);
    }

    if (!stristr($cwd, $importDir)) {
        $cwd = $importDir;
        xarModSetUserVar('filemanager', 'path.cwd', $importDir);
    }

    return $cwd;
}
?>