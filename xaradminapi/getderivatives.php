<?php
/**
 * get the list of derivative images (thumbnails and resized)
 *
 * @param   mixed   $fileId    (optional) The file id(s) of the image(s) we're looking for
 * @param   string  $fileName  (optional) The name of the image we're getting derivatives for
 * @param   string  $thumbsdir (optional) The directory where derivative images are stored
 * @param   string  $filematch (optional) Specific file match for derivative images
 * @returns array
 * @return array containing the list of derivatives
 * @todo add startnum and numitems support + cache for large # of images
 */
function images_adminapi_getderivatives($args)
{
    extract($args);
    if (empty($thumbsdir)) {
        $thumbsdir = xarModGetVar('images', 'path.derivative-store');
    }
    if (empty($thumbsdir)) {
        return array();
    }
    if (empty($filematch)) {
        $filematch = '';
        if (!empty($fileName)) {
            // Note: resized images are named [filename]-[width]x[height].jpg - see resize() method
            //$filematch = '^' . $fileName . '-\d+x\d+';
            // Note: processed images are named [filename]-[setting].[ext] - see process_image() function
            $filematch = '^' . $fileName . '-.+';
        }
    }
    if (empty($filetype)) {
        // Note: resized images are JPEG files - see resize() method
        //$filetype = 'jpg';
        // Note: processed images can be JPEG, GIF or PNG files - see process_image() function
        $filetype = '(jpg|png|gif)';
    }

    $files = xarModAPIFunc('dynamicdata','admin','browse',
                           array('basedir'   => $thumbsdir,
                                 'filematch' => $filematch,
                                 'filetype'  => $filetype));
    if (!isset($files)) return;

    if (!empty($fileId)) {
        if (!is_array($fileId)) {
            $fileId = array($fileId);
        }
    }

    $imagelist = array();
    $filenames = array();
    foreach ($files as $file) {
        // Note: resized images are named [filename]-[width]x[height].jpg - see resize() method
        if (preg_match('/^(.+?)-(\d+)x(\d+)\.jpg$/',$file,$matches)) {
            $id = md5($thumbsdir . '/' . $file);
            if (!empty($fileId)) {
                if (!in_array($id,$fileId)) continue;
            }
            $info = stat($thumbsdir . '/' . $file);
            $imagelist[] = array('fileLocation' => $thumbsdir . '/' . $file,
                                 'fileDownload' => $thumbsdir . '/' . $file,
                                 'fileName'     => $matches[1],
                                 'fileType'     => 'image/jpeg',
                                 'fileSize'     => $info['size'],
                                 'fileId'       => $id,
                                 'fileModified' => $info['mtime'],
                                 'width'        => $matches[2],
                                 'height'       => $matches[3]);
            $filenames[$matches[1]] = 1;

        // Note: processed images are named [filename]-[setting].[ext] - see process_image() function
        } elseif (preg_match('/^(.+?)-(.+?)\.\w+$/',$file,$matches)) {
            $id = md5($thumbsdir . '/' . $file);
            if (!empty($fileId)) {
                if (!in_array($id,$fileId)) continue;
            }
            $statinfo = stat($thumbsdir . '/' . $file);
            $sizeinfo = getimagesize($thumbsdir . '/' . $file);
            $imagelist[] = array('fileLocation' => $thumbsdir . '/' . $file,
                                 'fileDownload' => $thumbsdir . '/' . $file,
                                 'fileName'     => $matches[1],
                                 'fileType'     => $sizeinfo['mime'],
                                 'fileSize'     => $statinfo['size'],
                                 'fileId'       => $id,
                                 'fileModified' => $statinfo['mtime'],
                                 'fileSetting'  => $matches[2],
                                 'width'        => $sizeinfo[0],
                                 'height'       => $sizeinfo[1]);
            $filenames[$matches[1]] = 1;
        }
    }

// CHECKME: keep track of originals for server images too ?

    if (empty($fileName) && xarModIsAvailable('uploads')) {

        if (xarModGetVar('uploads', 'file.obfuscate-on-import') ||
            xarModGetVar('uploads', 'file.obfuscate-on-upload')) {
            $obfuscated = true;
        } else {
            $obfuscated = false;
        }

        $fileinfo = array();
        foreach (array_keys($filenames) as $file) {
        // CHECKME: verify this once derivatives can be created in sub-directories of thumbsdir
            // this is probably an obfuscated hash for some uploaded/imported file
            if ($obfuscated && preg_match('/^(.*\/)?[0-9a-f]{8}\d+$/i',$file)) {

                $fileinfo[$file] = xarModAPIFunc('uploads','user','db_get_file',
                                                 array('fileHash' => $file));

            // this is probably the filename without extension for some uploaded/imported file
            } elseif (preg_match('/^(.*\/)?\w+$/i',$file)) {

            // CHECKME: watch out for duplicates here too
                $fileinfo[$file] = xarModAPIFunc('uploads','user','db_get_file',
                                                 array('fileName' => $file . '.%'));
            }
        }
        if (count($fileinfo) > 0) {
            foreach (array_keys($imagelist) as $id) {
                $fileHash = $imagelist[$id]['fileName'];
                if (!empty($fileinfo[$fileHash])) {
                    $info = $fileinfo[$fileHash];
                // CHECKME: assume only one match here ?
                    $imagelist[$id]['original'] = array_pop($info);
                }
            }
        }
    }

    return $imagelist;
}

?>
