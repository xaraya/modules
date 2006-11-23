<?php
/**
 * Count the number of server images
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Images Module Development Team
 */
/**
 * count the number of server images
 *
 * @author mikespub
 * @param   string  $basedir   The directory where images are stored
 * @param   string  $baseurl   (optional) The corresponding base URL for the images
 * @param   string  $filetypes (optional) The list of file extensions to look for
 * @param   boolean $recursive (optional) Recurse into sub-directories or not (default TRUE)
 * @param   mixed   $fileId    (optional) The file id(s) of the image(s) we're looking for
 * @param   string  $fileName  (optional) The name of the image we're looking for
 * @param   string  $filematch (optional) Specific file match for images
 * @returns integer
 * @return the number of images
 */
function images_adminapi_countimages($args)
{
    extract($args);
    if (empty($basedir)) {
        return 0;
    }
    if (!isset($baseurl)) {
        $baseurl = $basedir;
    }

    // Note: fileId is a base 64 encode of the image location here, or an array of fileId's
    if (!empty($fileId)) {
        $files = array();
        if (!is_array($fileId)) {
            $fileId = array($fileId);
        }
        foreach ($fileId as $id) {
            $file = base64_decode($id);
            if (file_exists($basedir . '/' . $file)) {
                $files[] = $file;
            }
        }
        return count($files);

    } else {
        if (empty($filematch)) {
            $filematch = '';
            if (!empty($fileName)) {
                $filematch = '^' . $fileName;
            }
        }
        if (empty($filetypes)) {
            $filetype = '(gif|jpg|png)';
        } elseif (is_array($filetypes)) {
            $filetype = '(' . join('|',$filetypes) . ')';
        } else {
            $filetype = '(' . $filetypes . ')';
        }
        if (!isset($recursive)) {
            $recursive = true;
        }

        $params = array('basedir'   => $basedir,
                        'filematch' => $filematch,
                        'filetype'  => $filetype,
                        'recursive' => $recursive);

        $cachekey = md5(serialize($params));
        // get the number of images from temporary cache - see getimages()
        if (xarVarIsCached('Modules.Images','countimages.'.$cachekey)) {
            return xarVarGetCached('Modules.Images','countimages.'.$cachekey);
        } else {
            $files = xarModAPIFunc('dynamicdata','admin','browse',
                                   $params);
            if (!isset($files)) return;

            return count($files);
        }
    }

    return 0;
}

?>
