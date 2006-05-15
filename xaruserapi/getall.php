<?php
/**
* Get details on a file or folder
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
* Get details on a file or folder
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param   string $args['path'] relative path of folder to scan
* @return  array
* @returns array of item details
* @throws  BAD_PARAM, NO_PERMISSION
*/
function files_userapi_getall($args)
{
    // security check
    if (!xarSecurityCheck('ViewFiles')) return;

    extract($args);

    // set defaults
    if (!isset($path)) $path = '';

    // clean and validate the path (must be folder)
    $path = xarModAPIFunc('files', 'user', 'cleanpath', array('path' => $path, 'type' => 'folder'));
    if (empty($path) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get paths
    $archive_dir = xarModGetVar('files', 'archive_dir');
    $archive_realpath = realpath($archive_dir);
    $realpath = realpath("$archive_dir/$path");
    $viewpath = $path;

    // scan for files and folders
    $hd = opendir($realpath);
    $dirs = $files = array();
    while (false !== ($file = readdir($hd))) {
        if ($file == '..' && $path == '/') continue;
        if (!is_readable("$realpath/$file")) continue;
        is_dir("$realpath/$file") ? $dirs[] = $file : $files[] = $file;
    }

    // assemble sorted list
    natcasesort($dirs);
    natcasesort($files);
    $list = array_merge($dirs, $files);

    // prepare mime data for folders, since mime module doesn't do it
    $image_big = xarTplGetImage('fs-directory.png', 'mime');
    $image_small = xarTplGetImage('fs-directory-16x16.png', 'mime');
    if (empty($image_big)) $image_big = xarTplGetImage('fs-directory.png');
    if (empty($image_small)) $image_small = xarTplGetImage('fs-directory-16x16.png');
    if (empty($image_small)) $image_small = $image_big;
    $folderinfo = array('mime' => 'fs/directory',
        'image_big' => $image_big,
        'image_small' => $image_small);

    // scan files and get data on them
    $filedata = array();
    $units = array(xarML(''), xarML('K'), xarML('M'), xarML('G'));
    foreach ($list as $index => $item) {

        // don't show anything above archive dir
        if ($item == '..' && ($path == '' || $path == '/')) {
            continue;
        }

        // generate paths to item, replacing double slashes
        $fullrealpath = preg_replace("/\/+/", '/', "$realpath/$item");
        $fullviewpath = preg_replace("/\/+/", '/', "$viewpath/$item");

        // current dir is a special case...
        if ($item == '.') {
            $fullrealpath = $realpath;
            $fullviewpath = $viewpath;
        }

        // parent dir is a special case...
        if ($item == '..') {
            $fullrealpath = dirname($realpath);
            $fullviewpath = dirname($viewpath);
        }

        // get mime type and image
        if (is_dir($fullrealpath)) {
            extract($folderinfo);
        } else {
            $mime = xarModAPIFunc('mime', 'user', 'analyze_file',
                array('fileName' => $fullrealpath));
            $image_big = xarModAPIFunc('mime', 'user', 'get_mime_image',
                array('mimeType' => $mime));
            $image_small = xarModAPIFunc('mime', 'user', 'get_mime_image',
                array('mimeType' => $mime,
                    'fileSuffix' => '-16x16.png|-16x16.gif|.png|.gif'));
        }

        // make url-compatible path
        $urlpath = xarModAPIFunc('files', 'user', 'urlpath', array('path' => $fullviewpath));

        // make file size human-readable
        $hrsize = filesize($fullrealpath);
        $cnt = 0;
        $unit = '';
        while ($hrsize > 1000) {
            $hrsize /= 1024;
            $cnt++;
            $unit = $units[$cnt];
        }
        $hrsize = round($hrsize, 1).$unit;

        // get created and modified dates
        $created = filectime($fullrealpath);
        $modified = filemtime($fullrealpath);

        // assemble info into one big array
        $row = array(
            'file'          => $item,
            'realpath'      => $fullrealpath,
            'viewpath'      => $fullviewpath,
            'urlpath'       => $urlpath,
            'folder'        => $viewpath,
            'image_big'     => $image_big,
            'image_small'   => $image_small,
            'size'          => filesize($fullrealpath),
            'hrsize'        => $hrsize,
            'is_dir'        => is_dir($fullrealpath),
            'is_readable'   => is_readable($fullrealpath),
            'is_writeable'  => is_writable($fullrealpath),
            'is_executable' => is_executable($fullrealpath),
            'created'       => $created,
            'modified'      => $modified,
            'mime'          => $mime
        );

        // add entry to list
        $filedata[] = $row;
    }

    return $filedata;
}

?>
