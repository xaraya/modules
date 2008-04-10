<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Change to the specified directory within the local imports sandbox directory
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   string     dirName  The name of the directory (within the import sandbox) to change to
 *  @return string           The complete path to the new Current Working Directory within the sandbox
 */

function uploads_userapi_import_chdir( $args )
{
    extract ( $args );

    if (!isset($dirName) || empty($dirName)) {
        $dirName = NULL;
    }

    $cwd = xarModUserVars::get('uploads', 'path.imports-cwd');
    $importDir = xarModVars::get('uploads', 'path.imports-directory');

    if (!empty($dirName)) {
        if ($dirName == '...') {
            if (stristr($cwd, $importDir) && strlen($cwd) > strlen($importDir)) {
                $cwd = dirname($cwd);
                xarModUserVars::set('uploads', 'path.imports-cwd', $cwd);
            }
        } else {
            if (file_exists("$cwd/$dirName") && is_dir("$cwd/$dirName")) {
                $cwd = "$cwd/$dirName";
                xarModUserVars::set('uploads', 'path.imports-cwd', $cwd);
            }
        }
    } else {
        // if dirName is empty, then reset the cwd to the top level directory
        $cwd = xarModVars::get('uploads', 'path.imports-directory');
        xarModUserVars::set('uploads', 'path.imports-cwd', $cwd);
    }

    if (!stristr($cwd, $importDir)) {
        $cwd = $importDir;
        xarModUserVars::set('uploads', 'path.imports-cwd', $importDir);
    }

    return $cwd;
}
?>