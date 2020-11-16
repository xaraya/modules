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
 *  Retrieves an external file using the File scheme
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   array  uri     the array containing the broken down url information
 *  @return array          FALSE on error, otherwise an array containing the fileInformation
 */

function uploads_userapi_import_external_file($args)
{
    extract($args);

    if (!isset($uri) || !isset($uri['path'])) {
        return; // error
    }

    // create the URI
    $fileURI = "$uri[scheme]://$uri[path]";

    if (is_dir($uri['path']) || @is_dir(readlink($uri['path']))) {
        $descend = true;
    } else {
        $descend = false;
    }

    $fileList = xarMod::apiFunc(
        'uploads',
        'user',
        'import_get_filelist',
        array('fileLocation' => $uri['path'],
                                     'descend' => $descend)
    );

    if (empty($fileList) || (is_array($fileList) && !count($fileList))) {
        return array();
    }

    $list = array();
    foreach ($fileList as $location => $fileInfo) {
        if ($fileInfo['inodeType'] == _INODE_TYPE_DIRECTORY) {
            $list += xarMod::apiFunc(
                'uploads',
                'user',
                'import_get_filelist',
                array('fileLocation' => $location, 'descend' => true)
            );
            unset($fileList[$location]);
        }
    }

    $fileList += $list;
    unset($list);


    return $fileList;
}
