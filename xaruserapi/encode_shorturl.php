<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Example module development team
 * @param $args the function and arguments passed to xarModURL
 * @return string
 * @return path to be added to index.php for a short URL, or empty if failed
 */


function uploads_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }

    // if we don't have a fileId, can't do too much
    if (!isset($fileId) || empty($fileId)) {
        return;
    } else {
        $fileName = xarModAPIFunc('uploads', 'user', 'db_get_filename', array('fileId' => $fileId));

        if (!isset($fileName) || empty($fileName)) {
            // fileId is nonexistant...
            return;
        }

        $ext = end(explode('.', $fileName));
        $fileName = "$fileId.$ext";
    }

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'uploads';

    // specify some short URLs relevant to your module
    if ($func == 'download') {
        // check for required parameters
        if (!empty($fileId) && is_numeric($fileId)) {
            $path = '/' . $module . '/' . $fileName;
        }
    } else {
        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }

    return $path;
}

?>